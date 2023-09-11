export interface ICallbackRequest {
    data: {
        content?: unknown;
    };
}

export interface ICallback {
    success(req: ICallbackRequest): void;
    error(err: number): void;
    complete?(): void;
}

export enum ERestMethod {
    POST = 'POST',
    GET = 'GET',
    PUT = 'PUT',
    DELETE = 'DELETE'
}

type ArrayLengthMutationKeys = 'splice' | 'push' | 'pop' | 'shift' | 'unshift';
export type FixedLengthArray<T, L extends number, TObj = [T, ...T[]]> =
    Pick<TObj, Exclude<keyof TObj, ArrayLengthMutationKeys>>
    & {
        [I: number ]: T;
        readonly length: L;
        [Symbol.iterator](): IterableIterator<T>;
    }

export interface IImageSize {
    width: number;
    height: number;
}

export interface IDynamicImport {
    default: any;
}
