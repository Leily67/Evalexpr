<?php

/*
    input => array of strings
    operator stack => array of strings
    output => array of strings


    foreach($input as $elem) {
        elem est une string
        if($elem est un nombre) {
            s'envoie dans l'output
        } else {
            $elem est un opérateur

            if (elem est une parenthèse ouvrante '(') {
                envoi dans la stack
            } else if (elem est une parenthèse fermante ')' ) {
                on depile stack dans output jusqu'a la parenthèse ouvrante
                on enlève la parenthès ouvrante 
            } else {
                
                tant que elem a une priorité plus petite ou égal que le dernier elem de la stack
                    alors on dépile stack dans output

                envoi dans la stack
            }


        }

    }

    on depile la stack dans l'output

    */
/*
function find_neg_to_merge($input){
    for($i = 0; $i < count($input); $i++) {
        if($i == 0 && $i < count($input) - 1 &&  $input[$i] == "-" && is_numeric($input[$i + 1])) {
            return $i;
        } 
        if($i > 0 && $i < count($input) - 1 && $input[$i - 1] == "(" && $input[$i] == "-" && is_numeric($input[$i + 1])) {
            return $i;
        }
    }
    return -1;
}

function merge_neg($input){
    
    boucle inf 
    trouver l'index d'un "-" a fusionner
    si on a un "-" => on fusionne
    si on ne trouve pas de "-" => on quitte la boucle
    
    while(1) {
        $index = find_neg_to_merge($input);
        if($index < 0) {
            break;
        } else {
            array_splice($input, $index, 2, ["-" . $input[$index + 1]]);
        }
    }
 return $input;
}
*/


function find_neg_to_split($input){
    for($i = 0; $i < count($input); $i++) {
        if($i > 0 && is_numeric($input[$i - 1]) && $input[$i][0] == "-" && is_numeric($input[$i])) {
            return $i;
        }
    }
    return -1;
}

function split_neg($input){
    /*
    boucle inf 
    trouver l'index d'un "-" a split
    si on a un "-" => on split
    si on ne trouve pas de "-" => on quitte la boucle
    */
    while(1) {
        $index = find_neg_to_split($input);
        if($index < 0) {
            break;
        } else {
            array_splice($input, $index, 1, ["-", ltrim($input[$index], "-")]);
        }
    }
 return $input;
}

function npi($expr) {
    $expr = preg_replace("/\s+/", "", $expr);
    $expr = str_replace("-(", "-1*(", $expr);
    $input = preg_split(
        '~(-?\d*(?:\.\d+)?|[()*/+-])~',
        $expr,
        0,
        PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE
    );

    $input = split_neg($input);

    //tableau des priorités des operateurs
    $priorities = [
        '+' => 1,
        '-' => 1,
        '*' => 2,
        '/' => 2,
        '%' => 2,
        '(' => 0,
    ];

    $operators_stack = [];
    $output = [];
    
    foreach($input as $elem){

        // elem est une string
        //is_numeric => permet de dire si $elem est un nombre
        if(is_numeric($elem)){

            // si elem est un nombre s'envoi dans l'output
            array_push($output, floatval($elem));
        } else {

            // elem est un operateur
            if ($elem == '(') {

                //si elem est un operateur, on envoi dans operators_stack
                array_push($operators_stack, $elem);

            } else if ($elem == ')' ) {
               while (end($operators_stack) != '(') {
                $last_operator = array_pop($operators_stack);
                array_push($output, $last_operator);
               }
                array_pop($operators_stack);
            } else {
                while (end($operators_stack) && $priorities[$elem] <= $priorities[end($operators_stack)]) {
                    $last_operator = array_pop($operators_stack);
                    array_push($output, $last_operator);
                }
                array_push($operators_stack, $elem);
            }
        }
    }
    //on depile la stack dans l'output
    while(empty($operators_stack) == false) {
        $last_operator = array_pop($operators_stack);
        array_push($output, $last_operator);
    }
    return $output;
}

//conditions de calcul
function eval_expr($expr) {
    $output = npi($expr);

    $operations = [
        '+' => function ($a, $b) {
            return $a + $b;
        },
        '-' => function ($a, $b) {
            return $a - $b;
        },
        '*' => function ($a, $b) {
            return $a * $b;
        },
        '/' => function ($a, $b) {
            return $a / $b;
        },
        '%' => function ($a, $b) {
            return $a % $b;
        },
    ];
    
    /*

        tant que il y a plus d'un element dans l'output
            i = 0
            tant que l'element a l'index i est un nombre
                on incremente i
            on recupère les deux derniers elem du tableau avant opérateur 
            on fait le calcul des 2 nombres avec la liste des operation $operations[$op]($a, $b)
            on retourne le resultat de ce calcul
            on remplace les deux nombre et l'opérateur par le resultat => le zbeul

        on retourne le resultat final qui est dans la première case de l'output

    */

    while(count($output) > 1) {
        $i = 0;
        while(is_numeric($output[$i])) {
            $i ++;
        }
        $op = $output[$i];
        $a = $output[$i - 2];
        $b = $output[$i - 1];

        //$operations[$op]($a, $b);
        $result = $operations[$op]($a, $b);
        array_splice($output, $i-2, 3, [$result]);
    }

    return $output[0];
}



//var_dump(eval_expr("1 * 1 + 3"));