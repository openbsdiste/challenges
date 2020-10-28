<?php
  require_once __DIR__ . "/mcrypt.php";

  class App_Crypto {
        public static function encrypte ($quoi, $cle, $cid = 0) {
            $cle = substr ($cle . 'sjdfkglmsdfkjgmlkdfsjgmdlsfkjgmsdflkjgmsdflkjgmdfjdflkgmsdfjdlfkgÃ¹msldfgkÃ¹', 0, 32);
            if ($cid < 8) {
                $quoi .= chr (4);
                $ivSize = mcrypt_get_iv_size (MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
                $iv = mcrypt_create_iv ($ivSize, MCRYPT_RAND);
                $crypte = @mcrypt_encrypt (MCRYPT_RIJNDAEL_256, $cle, $quoi, MCRYPT_MODE_ECB, $iv);
                return base64_encode ($crypte);
            } else {
                $iv = openssl_random_pseudo_bytes (openssl_cipher_iv_length ('aes-256-cbc'));
                $crypte = openssl_encrypt ($quoi, 'aes-256-cbc', $cle, 0, $iv);
                return base64_encode ($crypte . '::' . $iv);
            }
        }

        public static function decrypte ($quoi, $cle, $cid = 0) {
            $cle = substr ($cle . 'sjdfkglmsdfkjgmlkdfsjgmdlsfkjgmsdflkjgmsdflkjgmdfjdflkgmsdfjdlfkgÃ¹msldfgkÃ¹', 0, 32);
            if ($cid < 8) {
                $quoi = base64_decode ($quoi);
                $contenu = @mcrypt_decrypt (MCRYPT_RIJNDAEL_256, $cle, $quoi, MCRYPT_MODE_ECB);
                $contenuTrim = rtrim ($contenu, "\0");
                return substr ($contenuTrim, 0, -1);
            } else {
                list ($contenu, $iv) = explode ('::', base64_decode ($quoi), 2);
                return openssl_decrypt ($contenu, 'aes-256-cbc', $cle, 0, $iv);
            }
        }

        public static function festelFloat ($float, $cle, $profondeur = 16) {
            $max = $profondeur + 1;
            while ($cle < 1000) {
                $cle *= 10;
            }
            while ($cle > 10000) {
                $cle = intval ($cle / 10);
            }
            $k = $cle;
            if (strpos ($float, '.') !== false) {
                $x = substr ($float, 0, strpos ($float, '.'));
                $y = substr ($float, strlen ($x) + 1);
            } else {
                $x = $float;
                $y = 0;
            }
            while ($profondeur > 0) {
                $wt = 2 * $profondeur - $max;
                $w = $wt * $wt * $k;
                $u = ($y  + $w) % 999999999999;
                $v = $x ^ $u;
                $x = $y;
                $y = $v;
                $profondeur--;
            }
            return ($y . "." . $x);
        }
    }
