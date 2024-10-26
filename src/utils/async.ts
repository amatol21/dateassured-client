export function waitUntilTrue(condition: () => boolean, maxTime?: number) {
    return new Promise<void>(resolve => {
        let startTime = Date.now()
        if (condition() === true) return resolve()
        let task = setInterval(() => {
            if (condition() == true) {
                clearInterval(task)
                resolve()
            } else if (maxTime !== undefined && Date.now() - startTime > maxTime) {
                clearInterval(task)
                resolve()
            }
        }, 50);
    })
}