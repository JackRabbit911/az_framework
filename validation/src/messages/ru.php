<?php

return [
    'required'      => 'Это поле обязательно для заполнения',
    'email'         => 'Введенные данные не соответствуют формату email',
    'username'      => 'Это поле допускает только буквы, цифры, пробел, @ и !',           
    'integer'       => 'Это поле принимает только целые числа',
    'alpha'         => 'Это поле допускает только латинские буквы',
    'alpha_num'     => 'Это поле допускает только цифры и латинские буквы',
    'alpha_space'   => 'Это поле допускает только латинские буквы и пробелы',
    'alpha_utf8'    => 'Это поле допускает только буквы',
    'alpha_num_utf8' => 'Это поле допускает только буквы и цифры',
    'alpha_space_utf8'=>'Это поле допускает только буквы и пробелы',
    'text_utf8'     => 'Введенные данные содержат недопустимые символы',
    'phone'         => 'Введенные данные не соответствуют формату номера телефона',
    'phone_strict'  => 'веденные данные не соответствуют формату номера телефона',
    'valid_date'    => 'веденные данные не соответствуют формату даты',
    'min_length'    => 'Длина строки должна быть не меньше :min символов',
    'max_length'    => 'Длина строки должна быть не больше :max символов',
    'length'        => 'Длина строки должна быть от :min до :max символов',
    'confirm'       => 'Введенные данные не соответствуют полю "Пароль"',
    'regexp'        => 'Введенные данные содержат недопустимые символы',
    'boolean'       => 'Невозможно привести к логическому типу',
    'maxValue'      => 'Значение не может превышать :max',
    'minValue'      => 'Значение не может быть меньше :min',
    'yes'           => 'Необходим утвердительный ответ',
    'maxWordsCount' => 'Длина строки должна быть не больше :max слов',
    'hex_color'     => 'Неверный код цвета',

    'required_one_of'=> 'Одно из полей :fields должно быть заполнено',
    'email_or_phone' => 'Введенные данные не соответствуют формату email или номера телефона',
    
    'default'       => 'Данные введены неверно',

    'success' => '',

    'notEmpty' => 'Необходимо выбрать файл',
    'size'  => 'Размер загружаемого файла превышает :size',
    'mime'  => 'Недопустимый mime-тип загружаемого файла',
    'ext'   => 'Недопустимый mime-тип загружаемого файла',
    'type'  => 'Недопустимый mime-тип загружаемого файла',
    'checkClientFilename' => 'Недопустимое имя файла',
    'uploaded' => 'Загружено файлов :uploaded, не удалось загрузить :failed',
    'uploadSuccess' => 'Файл загружен успешно',
    'UPLOAD_ERR_INI_SIZE' => 'Размер загружаемого файла превышает :size',
    'UPLOAD_ERR_FORM_SIZE' => 'Загруженный файл превышает директиву MAX_FILE_SIZE, указанную в HTML-форме.',
    'UPLOAD_ERR_PARTIAL' => 'Файл загружен только частично',
];
