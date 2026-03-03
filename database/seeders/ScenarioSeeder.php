<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Step;
use App\Models\Option;

class ScenarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $steps = [];
        $data = [
            // BLOCK 1: Intro
            ['slug' => 'intro', 'title' => 'Начало', 'description' => 'Тихий вечер. Вы на 7-м этаже. Внезапно дребезжит посуда, люстра начинает раскачиваться. Ваши действия?'],
            ['slug' => 'earthquake-hits', 'title' => 'Толчки усилилсь', 'description' => 'ТОЛЧКИ УСИЛИЛИСЬ! Гул нарастает, мебель падает. У вас 5 секунд, чтобы найти укрытие!', 'time_limit' => 5],
            // BLOCK 2: Surviving in first moments
            ['slug' => 'under-table', 'title' => 'Под столом', 'description' => 'Вы под столом, держитесь за его ножку. Тряска длится 40 секунд. Вокруг падает штукатурка. Что делаете дальше?'],
            ['slug' => 'door-frame', 'title' => 'Дверной проем', 'description' => 'Вы стоите в проеме. В современных домах это не самое безопасное место. Рядом упал шкаф, преградив путь. Тряска стихла. Ваши действия?'], 
            // BLOCK 3: After quakes
            ['slug' => 'utility-check', 'title' => 'После толчков', 'description' => 'Толчки прекратились. В квартире пахнет газом. Видны искры от проводки.'],
            ['slug' => 'emergency-bag', 'title' => 'Тревожный чемоданчик', 'description' => 'Нужно уходить. Вы берете "тревожный чемоданчик". Что в нем обязательно должно быть?'],
            // BLOCK 4: Evacuation
            ['slug' => 'stair-vs-lift', 'title' => 'Лестница или лифт', 'description' => 'Вы в коридоре. Лифт работает. Соседи бегут к нему. Как спуститесь?'],
            ['slug' => 'staircase-danger', 'title' => 'Лестница', 'description' => 'Лестница забита людьми. Началась паника. Что предпримете?'],
            // BLOCK 5: Outside
            ['slug' => 'outside-safety', 'title' => 'На улице', 'description' => 'Вы вышли из подъезда. Вокруг высотные дома и ЛЭП. Куда направитесь?'],
            ['slug' => 'aftershocks', 'title' => 'Афтершоки', 'description' => 'Вы на площади. Прошел слух, что будет новый удар через 5 минут. Что делать?'],
            // BLOCK 6: Finals
            ['slug' => 'failed', 'title' => 'Провал', 'description' => 'Ваше решение привело к критической ошибке. В реальной ситуации это могло стоить жизни. Хотите попробовать еще раз?'],
            ['slug' => 'succeed', 'title' => 'Успех', 'description' => 'Вы действовали грамотно и сохранили хладнокровие. Теперь вы готовы к ЧС.'],
        ];

        foreach ($data as $item) {
            $steps[$item['slug']] = Step::create([
                'slug' => $item['slug'],
                'title' => $item['title'],
                'description' => $item['description'],
                'time_limit' => $item['time_limit'] ?? null,
            ]);
        }

        $navigation = [
            'intro' => [
                ['text' => 'Выйти на балкон', 'next' => 'failed', 'correct' => false, 'points' => 0, 'fb' => 'Балконы — самое опасное место, они обрушаются первыми.'],
                ['text' => 'Сохранять спокойствие', 'next' => 'earthquake-hits', 'correct' => true, 'points' => 10, 'fb' => 'Верно. Паника — ваш главный враг.'],
            ],
            'earthquake-hits' => [
                ['text' => 'Под крепкий стол', 'next' => 'under-table', 'correct' => true, 'points' => 10, 'fb' => 'Отлично! Это защитит вас от падающих предметов.'],
                ['text' => 'Бежать к лифту', 'next' => 'failed', 'correct' => false, 'points' => 0, 'fb' => 'Лифт может застрять или обрушиться. Никогда не используйте его!'],
                ['text' => 'В дверной проем', 'next' => 'door-frame', 'correct' => false, 'points' => 2, 'fb' => 'В современных домах проемы не являются несущими. Это риск.'],
            ],
            'under-table' => [
                ['text' => 'Ждать остановки', 'next' => 'utility-check', 'correct' => true, 'points' => 10, 'fb' => 'Правильно. Передвижения во время толчков травмоопасны.'],
                ['text' => 'Бежать на лестницу', 'next' => 'failed', 'correct' => false, 'points' => 0, 'fb' => 'Лестничные пролеты часто обрушаются во время активной фазы.'],
            ],
            'door-frame' => [
                ['text' => 'Отодвинуть шкаф', 'next' => 'utility-check', 'correct' => true, 'points' => 5, 'fb' => 'Тряска стихла, нужно выбираться, пока нет афтершоков.'],
                ['text' => 'Ждать помощи', 'next' => 'utility-check', 'correct' => true, 'points' => 3, 'fb' => 'Разумно, если путь заблокирован, но лучше действовать самому, если возможно.'],
            ],
            'utility-check' => [
                ['text' => 'Перекрыть газ и свет', 'next' => 'emergency-bag', 'correct' => true, 'points' => 10, 'fb' => 'Верно. Это предотвратит пожар и взрыв.'],
                ['text' => 'Скорее выбежать', 'next' => 'emergency-bag', 'correct' => false, 'points' => 0, 'fb' => 'Опасно. Утечка газа может привести к взрыву, пока вы в подъезде.'],
            ],
            'emergency-bag' => [
                ['text' => 'Вода, фонарь, документы', 'next' => 'stair-vs-lift', 'correct' => true, 'points' => 10, 'fb' => 'Это базовый набор для выживания.'],
                ['text' => 'Ноутбук, еда, ценные украшения', 'next' => 'stair-vs-lift', 'correct' => false, 'points' => 0, 'fb' => 'Ценности не помогут вам выжить в первые 72 часа.'],
            ],
            'stair-vs-lift' => [
                ['text' => 'Только по лестнице', 'next' => 'staircase-danger', 'correct' => true, 'points' => 10, 'fb' => 'Единственный верный способ эвакуации.'],
                ['text' => 'На лифте', 'next' => 'failed', 'correct' => false, 'points' => 0, 'fb' => 'Лифты блокируются при землетрясении. Вы окажетесь в ловушке.'],
            ],
            'staircase-danger' => [
                ['text' => 'Прижаться к стене', 'next' => 'outside-safety', 'correct' => true, 'points' => 10, 'fb' => 'Так вы избежите давки и пропустите спецслужбы.'],
                ['text' => 'Проталкиваться вниз', 'next' => 'outside-safety', 'correct' => false, 'points' => 0, 'fb' => 'Паника и давка на лестнице — причина многих жертв.'],
            ],
            'outside-safety' => [
                ['text' => 'На стадион/площадь', 'next' => 'aftershocks', 'correct' => true, 'points' => 10, 'fb' => 'Верно. Нужно подальше от фасадов и проводов.'],
                ['text' => 'В машину у дома', 'next' => 'aftershocks', 'correct' => false, 'points' => 0, 'fb' => 'Здание может обрушиться прямо на ваш автомобиль.'],
            ],
            'aftershocks' => [
                ['text' => 'Доверять 112', 'next' => 'succeed', 'correct' => true, 'points' => 10, 'fb' => 'Информационная гигиена спасает от паники.'],
                ['text' => 'Вернуться домой', 'next' => 'failed', 'correct' => false, 'points' => 0, 'fb' => 'Афтершоки часто сильнее первых толчков. Входить в здание нельзя!'],
            ],
            'failed' => [
                ['text' => 'Начать заново', 'next' => 'intro', 'correct' => true, 'points' => 0, 'fb' => 'Удачи! В этот раз будьте внимательнее.'],
            ],
            'succeed' => [
                ['text' => 'Повторить квиз', 'next' => 'intro', 'correct' => true, 'points' => 0, 'fb' => 'Закрепление знаний — ключ к безопасности.'],
            ],
        ];

        foreach ($navigation as $currentSlug => $options) {
            foreach ($options as $optionData) {
                Option::create([
                    'step_id' => $steps[$currentSlug]->id,
                    'next_step_id' => $steps[$optionData['next']]->id,
                    'text' => $optionData['text'],
                    'feedback' => $optionData['fb'],
                    'is_correct' => $optionData['correct'],
                    'score_points' => $optionData['points'],
                ]);
            }
        }
    }
}
