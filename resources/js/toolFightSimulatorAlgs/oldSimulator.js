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


export default function oldSimulator(data) {
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

    const offStrengths = calculateOffStrenghts(data, unitClassification, unitStrengths, fightResult.attackerSurvivor)
    const defStrengths = calculateDefStrenghts(data, unitStrengths, offStrengths, wallAtFight, fightResult.defenderSurvivor, true)

    const offStrengthSum = offStrengths.infantry + offStrengths.cavalry
    const deffStrengthSum = defStrengths.infantry + defStrengths.cavalry

    const offLosses = calculateLosses(data, offStrengthSum, deffStrengthSum)
    const defLosses = calculateLosses(data, deffStrengthSum, offStrengthSum)

    fightResult = correctTroops(data, fightResult, offLosses, defLosses, unitClassification)
    //console.log(offStrengths, defStrengths, offLosses, defLosses, fightResult)

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

    if(calculateFor == 0 || other == 0) {
        return 0
    } else if(calculateFor > other) {
        return Math.pow(other / calculateFor, lossExponent)
    } else {
        return 1
    }
}


function correctTroops(data, result, offLosses, defLosses, unitClassification) {
    // offensive troops
    unitClassification.infantry.forEach(unitIdx => {
        const oldSurvivors = result.attackerSurvivor[unitIdx]
        const troopLosses = Math.round(oldSurvivors * offLosses)
        result.attackerSurvivor[unitIdx] = result.attacker[unitIdx] - troopLosses
        result.attackerLoss[unitIdx] = troopLosses
    })

    unitClassification.cavalry.forEach(unitIdx => {
        const oldSurvivors = result.attackerSurvivor[unitIdx]
        const troopLosses = Math.round(oldSurvivors * offLosses)
        result.attackerSurvivor[unitIdx] = result.attacker[unitIdx] - troopLosses
        result.attackerLoss[unitIdx] = troopLosses
    })

    // spy
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
        } else if(((spyCountDef + 1) / spyCountOff) >= spyRateTillDeath) {
            spyLosses = spyCountOff // no survivors
        } else {
            spyLosses = Math.round(spyCountOff * Math.pow((spyCountDef + 1) / (spyCountOff * spyRateTillDeath), lossExponent))
        }

        result.attackerSurvivor[unitIdx] = result.attacker[unitIdx] - spyLosses
        result.attackerLoss[unitIdx] = spyLosses
    })

    // defensive troops
    const unitCount = result.defender.length
    for(let unitIdx = 0; unitIdx < unitCount; unitIdx++) {
        const oldSurvivors = result.defenderSurvivor[unitIdx]
        const troopLosses = Math.round(oldSurvivors * defLosses)
        result.defenderSurvivor[unitIdx] = result.defender[unitIdx] - troopLosses
        result.defenderLoss[unitIdx] = troopLosses
    }

    return result
}
