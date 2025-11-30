import newSimulator from './toolFightSimulatorAlgs/newSimulator.js'
import oldSimulator from './toolFightSimulatorAlgs/oldSimulator.js'

function runSimulation(data, serverUnits) {
    if(data.newSimulator) {
        return newSimulator(data, serverUnits)
    } else {
        return oldSimulator(data, serverUnits)
    }
}

window.runSimulation = runSimulation
