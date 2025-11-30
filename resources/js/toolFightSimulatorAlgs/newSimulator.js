import {
    buildUnitClassification,
    getEffectiveWallLevel,
    internalToExternalArray,
    calculateOffStrenghts,
    calculateDefStrenghts,
    calculateNewWallLevel,
    getOldBuildingLevel,
    calculateCatapultDamage,
} from './shared.js'


export default function newSimulator(data, serverUnits) {
    const wallAtFight = getEffectiveWallLevel(data)
    const unitNames = Object.keys(data.attacker)
    const unitStrengths = unitNames.map(uName => serverUnits[uName])
    const unitClassification = buildUnitClassification(unitNames)

    let fightResult = {
        "attacker": unitNames.map(uName => data.attacker[uName]),
        "attackerLoss": unitNames.map(uName => 0),
        "attackerSurvivor": unitNames.map(uName => data.attacker[uName]),
        "defender": unitNames.map(uName => data.defender[uName]),
        "defenderLoss": unitNames.map(uName => 0),
        "defenderSurvivor": unitNames.map(uName => data.defender[uName]),
    }

    for(let i = 0; i < 3; i++) {
        const offStrengths = calculateOffStrenghts(data, unitClassification, unitStrengths, fightResult.attackerSurvivor)
        const defStrengths = calculateDefStrenghts(data, unitStrengths, offStrengths, wallAtFight, fightResult.defenderSurvivor, i==0)
        const offLosses = calculateLosses(data, offStrengths, defStrengths)
        const defLosses = calculateLosses(data, defStrengths, offStrengths)

        fightResult = correctTroops(data, fightResult, offStrengths, offLosses, defLosses, i==0, unitClassification)
        //console.log(offStrengths, defStrengths, offLosses, defLosses, fightResult)
    }

    const newWall = calculateNewWallLevel(data, serverUnits, fightResult, unitClassification)
    const oldBuilding = getOldBuildingLevel(data, newWall)
    const newBuilding = calculateCatapultDamage(data, serverUnits, fightResult, unitClassification, oldBuilding)

    const finalResult = {
        "attacker": internalToExternalArray(fightResult.attacker, unitNames),
        "attackerLoss": internalToExternalArray(fightResult.attackerLoss, unitNames),
        "attackerSurvivor": internalToExternalArray(fightResult.attackerSurvivor, unitNames),
        "defender": internalToExternalArray(fightResult.defender, unitNames),
        "defenderLoss": internalToExternalArray(fightResult.defenderLoss, unitNames),
        "defenderSurvivor": internalToExternalArray(fightResult.defenderSurvivor, unitNames),
        "wallOld": data.wall,
        "wallNew": newWall,
        "catapultOld": oldBuilding,
        "catapultNew": newBuilding,
    }
    return finalResult
}


function calculateLosses(data, calculateFor, other) {
    let lossExponent = 1.5
    if(data.farmLimit !== 0) {
        lossExponent = 1.6
    }

    let losses = {};
    
    ["infantry", "cavalry", "archer"].forEach(troopType => {
        if(calculateFor[troopType] == 0 || other[troopType] == 0) {
            losses[troopType] = 0
        } else if(calculateFor[troopType] > other[troopType]) {
            losses[troopType] = Math.pow(other[troopType] / calculateFor[troopType], lossExponent)
        } else {
            losses[troopType] = 1
        }
    })

    return losses
}


function correctTroops(data, result, offStrengths, offLosses, defLosses, spyRound, unitClassification) {
    // offensive troops
    unitClassification.infantry.forEach(unitIdx => {
        const oldSurvivors = result.attackerSurvivor[unitIdx]
        const newSurvivors = Math.round(oldSurvivors * (1 - offLosses.infantry))
        result.attackerSurvivor[unitIdx] = newSurvivors
        result.attackerLoss[unitIdx] = result.attacker[unitIdx] - newSurvivors
    })

    unitClassification.cavalry.forEach(unitIdx => {
        const oldSurvivors = result.attackerSurvivor[unitIdx]
        const newSurvivors = Math.round(oldSurvivors * (1 - offLosses.cavalry))
        result.attackerSurvivor[unitIdx] = newSurvivors
        result.attackerLoss[unitIdx] = result.attacker[unitIdx] - newSurvivors
    })

    unitClassification.archer.forEach(unitIdx => {
        const oldSurvivors = result.attackerSurvivor[unitIdx]
        const newSurvivors = Math.round(oldSurvivors * (1 - offLosses.archer))
        result.attackerSurvivor[unitIdx] = newSurvivors
        result.attackerLoss[unitIdx] = result.attacker[unitIdx] - newSurvivors
    })

    // spys
    if(spyRound) {
        const spyRateTillDeath = 2.0
        let lossExponent = 1.5
        if(data.farmLimit !== 0) {
            lossExponent = 1.6
        }

        unitClassification.spy.forEach(unitIdx => {
            const spyCountOff = result.attackerSurvivor[unitIdx]
            const spyCountDef = result.defenderSurvivor[unitIdx]

            let spyLosses = 0
            if(spyCountOff == 0) {
                spyLosses = 0 //no spy
            } else if(((spyCountDef+1) / spyCountOff) >= spyRateTillDeath) {
                spyLosses = spyCountOff // no survivors
            } else {
                spyLosses = Math.round(spyCountOff * Math.pow((spyCountDef + 1) / (spyCountOff * spyRateTillDeath), lossExponent))
            }

            result.attackerSurvivor[unitIdx] = result.attacker[unitIdx] - spyLosses
            result.attackerLoss[unitIdx] = spyLosses
        })
    }

    // defensive troops
    const totalOff = offStrengths.infantry + offStrengths.cavalry + offStrengths.archer
    const unitCount = result.defender.length
    const offStrengthFactor = offStrengths.infantry * defLosses.infantry + offStrengths.cavalry * defLosses.cavalry + offStrengths.archer * defLosses.archer
    const decreaseFactor = offStrengthFactor / ((totalOff == 0)?1:totalOff)
    for(let unitIdx = 0; unitIdx < unitCount; unitIdx++) {
        const oldSurvivors = result.defenderSurvivor[unitIdx]
        const newSurvivors = Math.round(oldSurvivors * (1 - decreaseFactor))
        result.defenderSurvivor[unitIdx] = newSurvivors
        result.defenderLoss[unitIdx] = result.defender[unitIdx] - newSurvivors
    }

    return result
}
