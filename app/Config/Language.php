<?php

// Default timezone and location
date_default_timezone_set('America/Sao_Paulo');
setlocale(LC_ALL, 'pt_BR.UTF-8');
setlocale(LC_NUMERIC, 'en_US.UTF-8');

// Default translation
I18n::setLanguage('pt-br');
I18n::setLanguagePath('');