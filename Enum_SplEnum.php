<?php

class SplEnumPlus extends SplEnum {
    static function hasKey($key) {
        $foundKey = false;
       
        try {
            $enumClassName = get_called_class();
            new $enumClassName($key);
            $foundKey = true;
        } finally {
            return $foundKey;
        }
    }
}

class Enum extends SplEnumPlus {
  const API_BASE_URL     = "http://103.25.130.94/api/";
  const TANENT_ID        = 1;
  const COUNTRY_ID        = 1;
}

echo "Hello"+Enum::hasKey("API_BASE_URL")."<br>";
echo "O hi"+Enum::API_BASE_URL;
?>
