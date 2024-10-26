const PREFIX = 'VS: '


const log = {
    info(...args) {
        console.log(PREFIX, ...args)
    },
    warn(...args) {
        console.warn(PREFIX, ...args)
    },
    error(...args) {
        console.error(PREFIX, ...args)
    }
}

export default log;