export interface IChannel {
    context: IObserver;
    callback(): void;
}

export type TChannels = Record<string, IChannel[]>;

export interface IUnknownInstallOptions {
    channels?: unknown;
    publish?: unknown;
    subscribe?: unknown;
}

export interface IObserver {
    channels: TChannels;
    subscribe(channel: string, fn: (...args: any) => void): void;
    unsubscribe(channel: string): boolean | IObserver;
    publish(channel: string, ...args: any): boolean | IObserver;
    installTo(obj: IUnknownInstallOptions): void;
}
