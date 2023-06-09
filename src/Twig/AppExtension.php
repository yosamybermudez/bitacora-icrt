<?php


namespace App\Twig;


use Faker\Factory;
use RRule\RRule;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Security;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('base64_encode', [$this, 'base64Encode']),
            new TwigFilter('base64_decode', [$this, 'base64Decode']),
            new TwigFilter('ldap_parent_ou', [$this, 'ldapParentOU']),
            new TwigFilter('ldap_parent_cn', [$this, 'ldapParentCN']),
            new TwigFilter('ldap_parent_ou_cn', [$this, 'ldapParentOUCN']),
            new TwigFilter('ldap_closest_parent_cn', [$this, 'ldapClosestParentCN']),
            new TwigFilter('ldap_closest_parent_ou', [$this, 'ldapClosestParentOU']),
            new TwigFilter('ldap_closest_parent_ou_cn', [$this, 'ldapClosestParentOUCN']),
            new TwigFilter('to_identifier', [$this, 'toIdentifier']),
            new TwigFilter('dc_to_domain', [$this, 'dcToDomain']),
            new TwigFilter('domain_to_dc', [$this, 'domainToDc']),
            new TwigFilter('dc_to_name', [$this, 'dcToName']),
        ];
    }

    public function dcToName(string $r){
        $result = explode(',', $r);
        $result = explode('=', $result[0]);
        return $result[1];
    }

    public function ldapParentOU(string $r){
        $result = explode(',', $r);
        $index = count($result);
        $array = [];
        while($index){
            $item = $result[--$index];
            $item = explode('=',$item);
            if(strtolower($item[0]) == "ou"){
                $array[] = $item[1];
            }
        }
        $result = implode(" / ", $array);
        return $result;
    }

    public function ldapClosestParentOU(string $r){
        $result = explode(',', $r);
        foreach($result as $item){
            $item = explode('=',$item);
            if(strtolower($item[0]) != "ou"){
                continue;
            }
            return $item[1];
        }
        return null;
    }

    public function base64Encode(string $text){
        return base64_encode($text);
    }

    public function base64Decode(string $text){
        return base64_decode($text);
    }

    public function ldapParentCN(string $r){
        $result = explode(',', $r);

        $index = count($result);
        $array = [];
        while($index){
            $item = $result[--$index];
            $item = explode('=',$item);

            if(strtolower($item[0]) == "cn"){
                $array[] = $item[1];
            }
        }
        array_pop($array);
        $result = implode(" / ", $array);
        return $result;
    }

    public function ldapClosestParentCN(string $r){
        $result = explode(',', $r);

        foreach($result as $item){
            $item = explode('=',$item);

            if(strtolower($item[0]) != "cn"){
                continue;
            }
            return $item[1];
        }

        return null;
    }

    public function ldapParentOUCN(string $r){
        $result = explode(',', $r);
        $index = count($result);
        $array = [];
        while($index){
            $item = $result[--$index];
            $item = explode('=',$item);
            if(strtolower($item[0]) == "ou" || strtolower($item[0]) == "cn"){
                $array[] = $item[1];
            }
        }

        array_pop($array);
        $result = implode(" / ", $array);
        return $result;
    }

    public function ldapClosestParentOUCN(string $r){

        $result = explode(',', $r);
        array_shift($result);
        foreach($result as $item){
            $item = explode('=',$item);
            if(strtolower($item[0]) != "cn" && strtolower($item[0]) != "ou"){
                continue;
            }
            return $item[1];
        }

        return null;
    }

    public function dcToDomain(string $dn, bool $at_sign = false){
        $result = explode(',', $dn);
        $parts = [];
        foreach($result as $item){
            $item = explode('=',$item);

            if(strtolower($item[0]) != "dc"){
                continue;
            }
            $parts[] = $item[1];
        }
        if(count($parts) !== 0){
            return ($at_sign ? '@' : '') . implode('.', $parts);
        }

        return null;
    }

    public function domainToDc(string $mail){
        $result = explode('@', $mail)[1];
        $result = explode('.', $result);
        $parts = [];
        foreach($result as $item){
            $parts[] = 'dc=' . $item;
        }
        if(count($parts) !== 0){
            return implode(',', $parts);
        }

        return null;
    }

    public function toIdentifier(string $r){
        $unwanted_array = array('Š'=>'S', 'š'=>'s', 'Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
            'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U',
            'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss', 'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c',
            'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o',
            'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y', ' ' => '_');
        $str = strtr($r, $unwanted_array);

        return strtolower($str);
    }
}