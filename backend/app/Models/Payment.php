<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    // модель пустая, и это хорошо показывает состояние merch payment flow:
    // таблица есть, но я не нашел кода который реально пишет сюда платежи после checkout
    // если бизнес ожидает audit trail по one-time payments, сейчас его просто нет
}
