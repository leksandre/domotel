<div class="grid__content-section">
    <div class="mortgage-calculator j-mortgage-calculator">
        <div class="mortgage-calculator__filter j-animation__item">
            <div class="mortgage-calculator__filter-item">
                <div class="mortgage-calculator__filter-heading">{{ trans('kelnik-mortgage::front.components.howToBuy.calc.form.price') }}</div>
                <div class="mortgage-calculator__filter-range">
                    <div class="range-slider j-range-slider">
                        @php
                            $disabled = !$calc->getMinPrice() || $calc->getMinPrice() === $calc->getMaxPrice();
                        @endphp
                        <div class="range-slider__input-container">
                            <input class="range-slider__input j-range-slider__input" type="text" value="{!! $calc->getMeanPrice() !!}" name="price[min]" autocomplete="off" data-digit="true" @if($disabled) disabled @endif>
                        </div>
                        <div class="range-slider__base">
                            <input id="price"
                                   type="text"
                                   class="j-range-slider__base"
                                   value=""
                                   data-min="{!! $calc->getMinPrice() !!}"
                                   data-max="{!! $calc->getMaxPrice() !!}"
                                   data-from="{!! $calc->getMeanPrice() !!}"
                                   data-to="{!! $calc->getMaxPrice() !!}"
                                   data-type="single"
                                   data-step="1000"
                                   data-min-interval="1000"
                                   data-digit="true"
                                   @if($disabled) data-disable="true" @endif
                                   autocomplete="off" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="mortgage-calculator__filter-item">
                <div class="mortgage-calculator__filter-heading">{{ trans('kelnik-mortgage::front.components.howToBuy.calc.form.firstPayment') }}</div>
                <div class="mortgage-calculator__filter-range">
                    <div class="range-slider j-range-slider">
                        <div class="range-slider__input-container">
                            <input class="range-slider__input j-range-slider__input" type="text" value="{!! $calc->getMeanFirstPayment() !!}" name="first-pay[min]" autocomplete="off" data-digit="true">
                            <span class="mortgage-calculator__filter-percent j-mortgage-calculator__percent">{!! $calc->getMeanFirstPaymentPercent() !!}%</span>
                        </div>
                        <div class="range-slider__base">
                            <input id="first-pay"
                                   type="text"
                                   class="j-range-slider__base"
                                   value=""
                                   data-min="{!! $calc->getMinFirstPayment() !!}"
                                   data-max="{!! $calc->getMaxFirstPayment() !!}"
                                   data-from="{!! $calc->getMeanFirstPayment() !!}"
                                   data-to="{!! $calc->getMaxFirstPayment() !!}"
                                   data-type="single"
                                   data-step="1000"
                                   data-min-interval="1000"
                                   data-digit="true"
                                   autocomplete="off" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="mortgage-calculator__filter-item">
                <div class="mortgage-calculator__filter-heading">{{ trans('kelnik-mortgage::front.components.howToBuy.calc.form.time') }}</div>
                <div class="mortgage-calculator__filter-range">
                    <div class="range-slider j-range-slider">
                        <div class="range-slider__input-container">
                            <input class="range-slider__input j-range-slider__input" type="text" value="{!! $calc->getMeanTime() !!}" name="limitation[min]" autocomplete="off" data-digit="true">
                        </div>
                        <div class="range-slider__base">
                            <input id="limitation"
                                   type="text"
                                   class="j-range-slider__base"
                                   value=""
                                   data-min="{!! $calc->getMinTime() !!}"
                                   data-max="{!! $calc->getMaxTime() !!}"
                                   data-from="{!! $calc->getMeanTime() !!}"
                                   data-to="{!! $calc->getMaxTime() !!}"
                                   data-type="single"
                                   data-step="1"
                                   data-min-interval="1"
                                   data-digit="true"
                                   autocomplete="off" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mortgage-calculator__mortgage j-mortgage-calculator__mortgage">
            <div class="mortgage-calculator__mortgage-heading j-mortgage-calculator__mortgage-heading">
                <div class="mortgage-calculator__mortgage-fit j-animation__item">
                    <span class="j-mortgage-calculator__orders-count"></span> {{ trans('kelnik-mortgage::front.components.howToBuy.calc.result.banksCount', ['cnt' => $banks->sum(fn(\Kelnik\Mortgage\Models\Bank $bank) => $bank->programs->count())]) }}
                </div>
                <div class="mortgage-calculator__mortgage-amount j-animation__item">
                    {{ trans('kelnik-mortgage::front.components.howToBuy.calc.result.amountTitle') }}
                    <span class="j-mortgage-calculator__mortgage-amount">{{ trans('kelnik-mortgage::front.components.howToBuy.calc.result.amount', ['value' => number_format($calc->getAmount(), 0, '.', ' ')]) }}</span>
                </div>
            </div>
            <div class="mortgage-calculator__container">
                <div class="mortgage-calculator__mortgage-programs">
                    <div class="mortgage-calculator__mortgage-programs-result j-mortgage-calculator__programs-result">
                        <div class="mortgage-calculator__programs-details j-animation__item">
                            <div class="mortgage-calculator__programs-detail">{{ trans('kelnik-mortgage::front.components.howToBuy.calc.result.header.bank') }}</div>
                            <div class="mortgage-calculator__programs-detail">{{ trans('kelnik-mortgage::front.components.howToBuy.calc.result.header.payment') }}</div>
                            <div class="mortgage-calculator__programs-detail">{{ trans('kelnik-mortgage::front.components.howToBuy.calc.result.header.rate') }}</div>
                            <div class="mortgage-calculator__programs-detail">{{ trans('kelnik-mortgage::front.components.howToBuy.calc.result.header.firstPayment') }}</div>
                            <div class="mortgage-calculator__programs-detail">{{ trans('kelnik-mortgage::front.components.howToBuy.calc.result.header.time') }}</div>
                        </div>
                        <div class="mortgage-calculator__programs-list">
                            @foreach($banks as $bank)
                                @foreach($bank->programs as $program)
                                    <div class="mortgage-calculator__program j-mortgage-calculator__program j-animation__item">
                                        <div class="mortgage-calculator__program-logo-wrapper">
                                            @if($bank->logo->exists)
                                                <img class="mortgage-calculator__program-logo" loading="lazy" data-src="{{ $bank->logoResizedPath ?: $bank->logo->url }}" alt="{{ $bank->title }}" width="48" height="48" class="bank-card__logo">
                                            @endif
                                        </div>
                                        <div class="mortgage-calculator__program-payment">
                                            <div class="mortgage-calculator__program-name">{{ $bank->title }}</div>
                                            <div class="mortgage-calculator__program-amount">
                                                <span class="j-mortgage-calculator__payment"></span>
                                            </div>
                                        </div>

                                        <div class="mortgage-calculator__program-rate j-mortgage-calculator__rate" data-rate="{!! $program->rate !!}">
                                            <div class="mortgage-calculator__program-name">{{ trans('kelnik-mortgage::front.components.howToBuy.calc.result.rate') }}</div>
                                            <div class="mortgage-calculator__program-value">{{ trans('kelnik-mortgage::front.components.howToBuy.calc.result.rateFrom', ['value' => $program->rate]) }}</div>
                                        </div>

                                        <div class="mortgage-calculator__program-percent j-mortgage-calculator__first-payment" data-first-payment="{!! $program->min_payment_percent !!}" data-first-payment-max={!! $program->max_payment_percent !!}>
                                            <div class="mortgage-calculator__program-name">{{ trans('kelnik-mortgage::front.components.howToBuy.calc.result.firstPayment') }}</div>
                                            <div class="mortgage-calculator__program-value">{{ trans('kelnik-mortgage::front.components.howToBuy.calc.result.firstPaymentFrom', ['value' => $program->min_payment_percent]) }}</div>
                                        </div>

                                        <div class="mortgage-calculator__program-range j-mortgage-calculator__limitation" data-limitation="{!! $program->max_time !!}" data-limitation-min="{!! $program->min_time !!}">
                                            <div class="mortgage-calculator__program-name">{{ trans('kelnik-mortgage::front.components.howToBuy.calc.result.time') }}</div>
                                            <div class="mortgage-calculator__program-value">{{ trans('kelnik-mortgage::front.components.howToBuy.calc.result.timeTo', ['value' => $program->max_time, 'plural' => trans_choice('kelnik-mortgage::front.components.howToBuy.calc.result.timeToPlural', $program->max_time)]) }}</div>
                                        </div>
                                        <div class="mortgage-calculator__program-about">{{ $program->comment ?: $program->title }}</div>
                                    </div>
                                @endforeach
                            @endforeach
                        </div>
                        @if(!empty($calcData['helpText']))<div class="mortgage-calculator__notice">{!! $calcData['helpText'] !!}</div>@endif
                    </div>
                    <div class="mortgage-calculator__mortgage-programs-no-result is-hidden j-mortgage-calculator__programs-no-result">
                        <svg width="106" height="64" viewBox="0 0 106 64" fill="none" xmlns="http://www.w3.org/2000/svg" class="mortgage-calculator__empty-result-icon">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M49.4144 40.2484C49.2062 39.424 48.998 38.5996 48.7845 37.7805C48.796 37.7714 48.8073 37.7623 48.8184 37.7534C48.8255 37.7476 48.8325 37.7419 48.8394 37.7363L48.8397 37.736C48.9224 37.6686 48.9957 37.6087 49.0834 37.5915C49.4636 37.7119 49.8438 37.8285 50.2229 37.9448L50.2234 37.9449C51.1001 38.2138 51.9704 38.4808 52.82 38.7887C55.1687 39.6288 56.6633 41.2671 57.0689 43.7245C57.1325 44.1191 57.1975 44.5137 57.2626 44.9083L57.2627 44.9084C57.6133 47.0346 57.9644 49.1632 58.0725 51.3067C58.1921 53.7622 58.1651 56.2383 58.1382 58.7134C58.1267 59.7738 58.1152 60.8341 58.1152 61.8925C58.1152 63.1947 58.7771 63.9298 59.9728 63.9928C61.446 64.0768 62.2788 63.4257 62.3215 62.0395L62.3215 62.0393C62.4069 59.5189 62.4923 56.9986 62.4282 54.4782C62.1259 44.2864 60.834 34.2069 59.5406 24.116C59.464 23.518 59.3873 22.92 59.3109 22.322C59.2468 21.9229 59.439 21.3348 59.7379 21.0828C63.4317 17.7852 65.631 13.6895 66.784 8.98477C67.3818 6.46436 67.5313 3.92294 66.7199 1.42353C66.3569 0.289346 65.3534 -0.23574 64.3072 0.100315C63.325 0.394363 62.8552 1.33952 63.1328 2.4527C63.2823 2.99879 63.4104 3.54488 63.4531 4.09097C63.8161 9.40484 61.1258 13.2485 57.133 16.357C55.3608 17.7432 51.3254 17.9112 49.3396 16.0629C48.974 15.7185 48.5913 15.3908 48.2091 15.0636C47.5426 14.4929 46.8777 13.9236 46.3077 13.2695C43.8309 10.455 42.4217 7.26249 43.0409 3.43986C43.2331 2.22166 42.7206 1.29751 41.6744 1.08748C40.5214 0.877443 39.5606 1.46554 39.3898 2.72575C39.2403 3.90194 39.1122 5.12014 39.2403 6.27533C39.8809 11.7362 42.5498 16.231 46.6707 19.8015C48.3148 21.2298 49.2756 22.574 49.3183 24.7584C49.3477 26.4091 49.5294 28.0598 49.7164 29.7587C49.8006 30.524 49.8859 31.2992 49.9588 32.0886C49.7622 32.0425 49.5944 32.0086 49.4427 31.9779C49.2483 31.9386 49.0805 31.9047 48.9126 31.8575C48.4641 31.7324 48.0157 31.5929 47.5665 31.4531L47.5659 31.4529L47.5658 31.4529L47.5657 31.4528C46.5825 31.1469 45.5961 30.8399 44.5996 30.6813C43.895 30.5763 42.9342 30.8494 42.4004 31.3114C41.9947 31.6265 41.8666 32.6347 42.0587 33.1807C43.7455 37.7805 45.5177 42.3592 47.2899 46.917C47.6742 47.9462 48.6137 48.3872 49.5959 48.1142C50.5353 47.8411 51.0691 46.938 50.8343 45.8668C50.6933 45.2367 50.5294 44.6067 50.3654 43.9766C50.2561 43.5565 50.1467 43.1364 50.0443 42.7163C49.8307 41.8972 49.6226 41.0728 49.4144 40.2484ZM47.4117 54.9832C47.7697 54.9309 48.1276 54.8785 48.4856 54.8353C48.4642 54.7303 48.4642 54.6463 48.4642 54.5202C47.9863 54.2656 47.5126 54.0089 47.0397 53.7528L47.0385 53.7521L47.0384 53.752C45.9906 53.1844 44.9473 52.6192 43.8736 52.0838C43.3612 51.8318 42.742 51.6428 42.1655 51.6428C39.579 51.6188 36.9995 51.6222 34.419 51.6256H34.4175C32.4824 51.6282 30.5468 51.6308 28.6072 51.6218C27.0262 51.6156 25.4415 51.622 23.8553 51.6284C20.0005 51.6439 16.1368 51.6594 12.2945 51.4958C7.17013 51.2857 3.26278 46.3079 3.86063 41.2461C4.47983 36.0792 9.36935 32.1726 14.4297 32.9917C15.4759 33.1609 16.4837 33.5666 17.5111 33.9803L17.5111 33.9803C18.0143 34.1829 18.5222 34.3874 19.0417 34.567C19.5327 30.4713 20.9633 26.6487 25.4471 24.9474C29.8883 23.2461 33.6462 24.5693 36.6568 27.9719C37.7884 27.3418 38.8346 26.7537 39.9449 26.1236C37.1906 22.448 33.5181 20.4317 28.9061 20.5367C22.7568 20.6627 18.636 23.8552 16.2873 29.3581C15.9706 29.331 15.6607 29.2974 15.3552 29.2643L15.3551 29.2642L15.3551 29.2642L15.3549 29.2642C14.7129 29.1946 14.0909 29.1271 13.4689 29.1271C12.5507 29.1481 11.6113 29.2321 10.7145 29.4211C3.96739 30.8074 -0.580517 36.7723 0.0600327 43.3464C0.700582 50.0675 6.31607 55.1924 13.2554 55.2344C23.7817 55.2974 34.3081 55.2764 44.8344 55.2344C45.6935 55.2344 46.5526 55.1088 47.4117 54.9832ZM67.1042 55.2554V51.6428H68.5134C71.1326 51.6428 73.7517 51.6451 76.3708 51.6474H76.374C81.6113 51.6521 86.8485 51.6568 92.0857 51.6428C96.9111 51.6218 100.669 49.0173 101.822 44.8167C102.868 40.952 101.865 37.5705 98.7474 34.966C95.5446 32.2986 91.9148 32.1726 88.157 33.7478C87.9968 33.8213 87.8367 33.9001 87.6766 33.9789C87.5164 34.0576 87.3563 34.1364 87.1961 34.2099C87.1786 34.2272 87.1465 34.216 87.1001 34.1998C87.09 34.1963 87.0794 34.1926 87.068 34.1889C86.935 33.7308 86.8116 33.2664 86.6879 32.8005C86.494 32.0702 86.2991 31.3364 86.0645 30.6183C84.762 26.6697 81.1323 24.1073 76.9473 24.1283C72.7624 24.1493 69.154 26.7537 67.8942 30.7023C67.612 31.579 67.4125 32.4862 67.2093 33.4098L67.2092 33.4102C67.1204 33.814 67.0308 34.221 66.9334 34.63C65.9299 34.2309 65.268 33.6848 65.268 32.4456C65.268 31.8928 65.177 31.3399 65.086 30.787C65.0007 30.2687 64.9153 29.7504 64.905 29.2321C64.8837 28.539 64.9691 27.7409 65.3107 27.1738C68.1078 22.469 72.314 20.0956 77.8655 20.4527C83.182 20.7887 86.9399 23.5612 89.2032 28.2869C89.3313 28.539 89.4381 28.791 89.5448 29.0641C89.5662 29.1271 89.6302 29.1691 89.6943 28.959C90.3868 29.0192 91.0859 29.0495 91.7845 29.0799C93.2829 29.145 94.7795 29.21 96.2065 29.5681C102.228 31.0384 106.434 36.8984 105.964 42.8213C105.473 49.3324 100.712 54.3942 94.3489 55.1083C92.8152 55.2802 91.2462 55.2785 89.7061 55.2767C89.6145 55.2766 89.523 55.2765 89.4316 55.2764C89.3697 55.2764 89.3077 55.2764 89.2459 55.2764C84.6497 55.2904 80.0536 55.2857 75.4511 55.281L75.4446 55.281C73.1432 55.2787 70.8402 55.2764 68.5348 55.2764C68.1291 55.2554 67.6594 55.2554 67.1042 55.2554ZM59.2041 9.7409C59.2468 6.61138 56.6846 4.06997 53.4819 4.04896C50.3005 4.04896 47.6956 6.56938 47.6529 9.65688C47.6315 12.7654 50.2364 15.3698 53.3965 15.3698C56.6206 15.3908 59.1614 12.8914 59.2041 9.7409Z"></path>
                        </svg>
                        <div class="mortgage-calculator__no-result-heading">{{ trans('kelnik-mortgage::front.components.howToBuy.calc.noResult.title') }}</div>
                        <div class="mortgage-calculator__notice">{{ trans('kelnik-mortgage::front.components.howToBuy.calc.noResult.text') }}</div>
                        <button class="button button_theme_white mortgage-calculator__reset j-mortgage-calculator__reset" type="button">{{ trans('kelnik-mortgage::front.components.howToBuy.calc.noResult.button') }}</button>
                    </div>
                </div>
                @php
                    $hasForm = !empty($calcData['buttons']['consult']) || !empty($calcData['buttons']['mortgage']);
                    $hasFactoid = !empty($calcData['text']) || !empty($calcData['phone']) || !empty($calcData['schedule']);
                @endphp
                @if($hasFactoid || $hasForm)
                    <div class="mortgage-calculator__contact j-animation__item">
                        @if($hasFactoid)
                            <div class="mortgage-calculator__contact-info">
                                @if(!empty($calcData['text']))<div class="mortgage-calculator__contact-text">{!! $calcData['text'] !!}</div>@endif
                                @if(!empty($calcData['phone']))<a href="tel:{{ $calcData['phoneLink'] }}" class="mortgage-calculator__contact-number">{{ $calcData['phone'] }}</a>@endif
                                @if(!empty($calcData['schedule']))<div class="mortgage-calculator__notice">{{ $calcData['schedule'] }}</div>@endif
                            </div>
                        @endif
                        @if($hasForm)
                            <div class="mortgage-calculator__contact-info">
                                @if(!empty($calcData['buttons']['mortgage']))
                                    <x-kelnik-form :params="$calcData['buttons']['mortgage']" />
                                @endif
                                @if(!empty($calcData['buttons']['consult']))
                                    <x-kelnik-form :params="$calcData['buttons']['consult']" />
                                @endif
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
