<?php

return [

    'required' => ':field không được để trống.',
    'max' => [
        'string' => ':field không được lớn hơn :max kí tự.',
    ],
    'date_format' => ':field không đúng định dạng :format.',
    'after_or_equal' => ':from không được lớn hơn :to',
    'unique' => ':field không được trùng. Giá trị vừa nhập đã có trong Cơ sở dữ liệu',
    'exists_db' => ':field không tồn tại.',
    'array' => ':field phải là một mảng',
    'numeric' => ':field phải là kiểu số',
    'string' => ':field phải là một chuỗi ký tự',
    'date' => ':field phải có định dạng ngày tháng năm (yyyy-MM-dd hh:mm:ss)',
    'boolean' => ':field phải có giá trị true/false'
];
