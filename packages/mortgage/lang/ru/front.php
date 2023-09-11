<?php

declare(strict_types=1);

return [
    'components' => [
        'howToBuy' => [
            'button' => [
                'text' => 'Заказать консультацию'
            ],
            'rate' => 'Ставка',
            'firstPayment' => 'Первый взнос',
            'from' => 'от',
            'termYears' => 'Срок, лет',
            'termTo' => 'Срок, до',
            'termToPlural' => 'лет|год|лет',
            'calc' => [
                'form' => [
                    'price' => 'Стоимость квартиры, ₽',
                    'firstPayment' => 'Первый взнос, ₽',
                    'time' => 'Срок ипотеки, лет'
                ],
                'result' => [
                    'banksCount' => 'банков из :cnt',
                    'amountTitle' => 'Сумма ипотеки',
                    'amount' => ':value ₽',
                    'rateFrom' => 'от :value%',
                    'firstPaymentFrom' => 'от :value%',
                    'timeTo' => 'до :value :plural',
                    'timeToPlural' => 'лет|год|лет',
                    'rate' => 'Ставка',
                    'firstPayment' => 'Первый взнос',
                    'time' => 'Срок',
                    'header' => [
                        'bank' => 'Банк',
                        'payment' => 'Ежемесячный платеж',
                        'rate' => 'Ставка',
                        'firstPayment' => 'Первый взнос',
                        'time' => 'Срок ипотеки'
                    ]
                ],
                'noResult' => [
                    'title' => 'Нет подходящих предложений',
                    'text' => 'Попробуйте изменить условия поиска',
                    'button' => 'Сбросить параметры'
                ],
                'buttons' => [
                    'consult' => 'Консультация',
                    'mortgage' => 'Получить одобрение'
                ]
            ]
        ]
    ]
];
