
const WALL_BASIC_DEFENSE = [
    20, 97, 194, 272, 340, 402, // 0 - 5
    459, 512, 562, 610, 656, // 6 - 10
    699, 742, 783, 822, 861, // 11 - 15
    898, 935, 971, 1006, 1040 // 16 - 20
]


export function getEffectiveWallLevel(data) {
    const ramCount = data.attacker.ram

    if(! data.attackerBelieve) {
        ramCount /= 2
    }

    const wallAtFight = data.wall - Math.round(ramCount / (4 * Math.pow(1.09, data.wall)))

    if(wallAtFight < Math.round(data.wall / 2)) {
        return Math.round(data.wall / 2)
    }
    return wallAtFight
}


export function buildUnitClassification(unitNames) {
    let result = {
        infantry: [],
        cavalry: [],
        archer: [],
        spy: []
    }

    unitNames.forEach((unitName, idx) => {
        switch(unitName) {
            case "spear":
            case "sword":
            case "axe":
            case "ram":
            case "snob":
            case "catapult":
                result.infantry.push(idx)
                break

            case "light":
            case "heavy":
            case "knight":
                result.cavalry.push(idx)
                break

            case "archer":
            case "marcher":
                result.archer.push(idx)
                break

            case "spy":
                result.spy.push(idx)
                break
        }
    })

    return result
}


export function calculateOffStrenghts(data, unitClassification, serverUnits, amounts) {
    const moraleFactor = data.morale / 100
    const luckFactor = (data.luck + 100) / 100
    const believeFactor = data.attackerBelieve?1:0.5
    const offFactor = moraleFactor * luckFactor * believeFactor

    return {
        infantry: offFactor * unitClassification.infantry.map(idx => amounts[idx]*serverUnits[idx].attack).reduce((prev, cur) => prev + cur, 0),
        cavalry: offFactor * unitClassification.cavalry.map(idx => amounts[idx]*serverUnits[idx].attack).reduce((prev, cur) => prev + cur, 0),
        archer: offFactor * unitClassification.archer.map(idx => amounts[idx]*serverUnits[idx].attack).reduce((prev, cur) => prev + cur, 0),
    }
}


export function calculateDefStrenghts(data, serverUnits, offStrengths, wallAtFight, amounts, useBasicDefense) {
    let totalOff = offStrengths.infantry + offStrengths.cavalry + offStrengths.archer
    if(totalOff == 0) {
        totalOff = 1 // avoid divide by 0
    }
    const inantryMult = offStrengths.infantry / totalOff
    const cavalryMult = offStrengths.cavalry / totalOff
    const archerMult = offStrengths.archer / totalOff

    const believeFactor = data.defenderBelieve?1:0.5
    const nightBonus = data.nightBonus
    const farmFactor = 1.0
    if(data.farmLimit !== 0) {
        /*
        if (ServerSettings.getSingleton().getFarmLimit() != 0) {
            double limit = getFarmLevel() * ServerSettings.getSingleton().getFarmLimit();
            double defFarmUsage = calculateDefFarmUsage();
            double factor = limit / defFarmUsage;
            if (factor > 1.0) {
                factor = 1.0;
            }
            defStrength = factor * defStrength;
        }
        */
        throw new Error("Farmlimit is not supported")
    }

    const defFactor = believeFactor * farmFactor * nightBonus * Math.pow(1.037, wallAtFight)
    const basicDefense = useBasicDefense ? WALL_BASIC_DEFENSE[wallAtFight] : 0

    return {
        infantry: inantryMult * (basicDefense + defFactor * amounts.map((amount, idx) => amount*serverUnits[idx].defense).reduce((prev, cur) => prev + cur, 0)),
        cavalry: cavalryMult * (basicDefense + defFactor * amounts.map((amount, idx) => amount*serverUnits[idx].defense_cavalry).reduce((prev, cur) => prev + cur, 0)),
        archer: archerMult * (basicDefense + defFactor * amounts.map((amount, idx) => amount*serverUnits[idx].defense_archer).reduce((prev, cur) => prev + cur, 0)),
    }
}


export function internalToExternalArray(values, keys) {
    return keys.reduce((acc, key, i) => {
        acc[key] = values[i]
        return acc
    }, {})
}


export function calculateNewWallLevel(data, serverUnits, result, unitClassification) {
    const ramCount = data.attacker.ram

    if(! data.attackerBelieve) {
        ramCount /= 2
    }

    if(ramCount <= 0) {
        return data.wall // no rams no change
    }

    const maxDecrement = ramCount * serverUnits.ram.attack / (4 * Math.pow(1.09, data.wall))
    if(result.attackerSurvivor.some(amount => amount > 0)) {
        // win
        let lostUnits = 0
        let totalUnits = 0

        const allUnitsNoSpy = [...unitClassification.infantry, ...unitClassification.cavalry, ...unitClassification.archer]
        allUnitsNoSpy.forEach(unitIdx => {
            totalUnits += result.attacker[unitIdx]
            lostUnits += result.attackerLoss[unitIdx]
        })

        const lossRatio = lostUnits / totalUnits
        const wallDecrement = Math.round(maxDecrement * (1 - (lossRatio / 2)))
        const newWall = data.wall - wallDecrement
        return (newWall < 0) ? 0 : newWall
    } else {
        // loss
        const lostUnits = result.defenderLoss.reduce((prev, cur) => prev + cur, 0)
        const totalUnits = result.defender.reduce((prev, cur) => prev + cur, 0)

        const lossRatio = lostUnits / totalUnits
        const wallDecrement = Math.round(lossRatio * maxDecrement / 2)
        const newWall = data.wall - wallDecrement

        return (newWall < 0) ? 0 : newWall
    }
}


export function getOldBuildingLevel(data, wallAfterFight) {
    if(data.catapultTargetsWall) {
        return wallAfterFight
    }
    return data.catapultBuilding
}


export function calculateCatapultDamage(data, serverUnits, result, unitClassification, buildingLevel) {
    const cataCount = data.attacker.catapult
    if(cataCount <= 0) {
        return buildingLevel
    }

    if(! data.attackerBelieve) {
        //if attacker does not believe, cata fight at half power
        cataCount /= 2
    }

    const maxDecrement = getMaxCatapultDecrement(data, cataCount, buildingLevel, serverUnits)
    let buildingDecrement = 0
    if(result.attackerSurvivor.some(amount => amount > 0)) {
        // win
        let lostUnits = 0
        let totalUnits = 0

        const allUnitsNoSpy = [...unitClassification.infantry, ...unitClassification.cavalry, ...unitClassification.archer]
        allUnitsNoSpy.forEach(unitIdx => {
            totalUnits += result.attacker[unitIdx]
            lostUnits += result.attackerLoss[unitIdx]
        })

        const lossRatio = lostUnits / totalUnits
        buildingDecrement = Math.round(maxDecrement * (1 - (lossRatio / 2)))
    } else {
        // loss
        const lostUnits = result.defenderLoss.reduce((prev, cur) => prev + cur, 0)
        const totalUnits = result.defender.reduce((prev, cur) => prev + cur, 0)
        const lossRatio = lostUnits / totalUnits
        buildingDecrement = Math.round(lossRatio * maxDecrement / 2)
    }

    const newBuilding = buildingLevel - buildingDecrement
    return (newBuilding < 0) ? 0 : newBuilding
}


function getMaxCatapultDecrement(data, cataCount, buildingLevel, serverUnits) {
    if(data.cataChurch && buildingLevel <= 3) {
        //cata is aiming at the church
        switch(buildingLevel) {
            case 1:
                return cataCount / 800
            case 2:
                return cataCount / 333
            case 3:
                return cataCount / 240
            default:
                return -1
        }
    } else {
        //cata is aiming elsewhere
        return cataCount * serverUnits.catapult.attack / (300 * Math.pow(1.09, buildingLevel));
    }
}
