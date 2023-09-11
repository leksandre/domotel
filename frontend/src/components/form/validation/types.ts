export interface IValidationOptions {
    formSuccess?: string;
    formError?: string;
    inputSuccess?: string;
    inputError?: string;
    inputKey?: string;
    phoneMask?: string;
    emailReg?: RegExp;
    numberReg?: RegExp;
    lettersReg?: RegExp;
    urlReg?: RegExp;
    keyDelay?: number;
    separatorPrice?: string;
    separatorDecimal?: string;
    decimalDigits?: number;
}

export interface IValidationOptionsR {
    formSuccess: string;
    formError: string;
    inputSuccess: string;
    inputError: string;
    inputKey: string;
    phoneMask: string;
    emailReg: RegExp;
    numberReg: RegExp;
    lettersReg: RegExp;
    urlReg: RegExp;
    keyDelay: number;
    separatorPrice: string;
    separatorDecimal: string;
    decimalDigits: number;
}

export interface IValidationMessages {
    number?: string;
    regexp?: string;
    min?: string;
    max?: string;
    tel?: string;
    required?: string;
    email?: string;
    letters?: string;
    url?: string;
    mask?: string;
}

export interface IValidationMessagesR {
    number: string;
    regexp: string;
    min: string;
    max: string;
    tel: string;
    required: string;
    email: string;
    letters: string;
    url: string;
    mask: string;
}

export interface IValidateInputResult {
    result: boolean;
    error?: string;
    message?: string;
}

export interface IValidation {
    update(): void;
}
