<?php

namespace App\Http\Requests\Api\V1;

class UpdateContactRequest extends StoreContactRequest
{
    // 中身は空っぽで大丈夫です！
    // StoreContactRequest の authorize() や rules() を自動でそのまま引き継ぎます。
}