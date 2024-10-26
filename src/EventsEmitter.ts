import AppEvent from "./enums/AppEvent"

type EventHandler = (a?: any) => void

export default class EventsEmitter
{
    private _handlers : Map<AppEvent, Set<EventHandler>> = new Map<AppEvent, Set<EventHandler>>()

    protected addEventHandler(event: AppEvent, handler: EventHandler): void
    {
        if (!this._handlers.has(event)) this._handlers.set(event, new Set<EventHandler>())
        this._handlers.get(event).add(handler)
    }

    protected dispatchEvent(event: AppEvent, payload?: any): void
    {
        if (!this._handlers.has(event)) return
        for (let handler of this._handlers.get(event)) {
            handler(payload)
        }
    }
}