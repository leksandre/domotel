import type {IChannel, IObserver, IUnknownInstallOptions, TChannels} from './types/observer';

let instance: IObserver | null = null;

/* eslint-disable consistent-this */
class Observer implements IObserver {
    public channels: TChannels;

    constructor() {
        if (!instance) {
            instance = this;
        }

        this.channels = {};

        return instance;
    }

    public subscribe(channel: string, fn: (...args: any[]) => void): void {
        if (!this.channels[channel]) {
            this.channels[channel] = [];
        }

        this.channels[channel]!.push({
            context : this,
            callback: fn
        });
    }

    public unsubscribe(channel: string): boolean | IObserver {
        if (!this.channels[channel]) {
            return false;
        }

        delete this.channels[channel];

        return this;
    }

    public publish(channel: string, ...args: unknown[]): boolean | IObserver {
        if (!this.channels[channel]) {
            return false;
        }

        this.channels[channel]!.forEach((subscription: IChannel) => {
            subscription.callback.apply(subscription.context, args as []);
        });

        return this;
    }

    public installTo(obj: IUnknownInstallOptions): void {
        obj.channels = {};

        obj.publish = this.publish;
        obj.subscribe = this.subscribe;
    }
}

export default Observer;
/* eslint-enable consistent-this */

