<?php
/**
 * Arquivo de funções utilitárias para o sistema GoTicket
 */

/**
 * Formata um valor monetário para exibição
 * @param float $valor Valor a ser formatado
 * @return string Valor formatado como moeda brasileira
 */
function formatarMoeda($valor) {
    return 'R$ ' . number_format($valor, 2, ',', '.');
}

/**
 * Formata uma data para o padrão brasileiro
 * @param string $data Data no formato YYYY-MM-DD
 * @return string Data no formato DD/MM/YYYY
 */
function formatarData($data) {
    if (empty($data)) return '';
    return date('d/m/Y', strtotime($data));
}

/**
 * Formata um horário para exibição
 * @param string $horario Horário no formato HH:MM:SS
 * @return string Horário no formato HH:MM
 */
function formatarHorario($horario) {
    if (empty($horario)) return '';
    return date('H:i', strtotime($horario));
}

/**
 * Gera um código aleatório para ingressos
 * @param int $tamanho Tamanho do código
 * @return string Código gerado
 */
function gerarCodigoAleatorio($tamanho = 8) {
    $caracteres = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $codigo = '';
    for ($i = 0; $i < $tamanho; $i++) {
        $codigo .= $caracteres[rand(0, strlen($caracteres) - 1)];
    }
    return $codigo;
}

/**
 * Limpa uma string para uso seguro em URLs
 * @param string $string String a ser limpa
 * @return string String limpa
 */
function limparString($string) {
    $string = preg_replace('/[áàãâä]/ui', 'a', $string);
    $string = preg_replace('/[éèêë]/ui', 'e', $string);
    $string = preg_replace('/[íìîï]/ui', 'i', $string);
    $string = preg_replace('/[óòõôö]/ui', 'o', $string);
    $string = preg_replace('/[úùûü]/ui', 'u', $string);
    $string = preg_replace('/[ç]/ui', 'c', $string);
    $string = preg_replace('/[^a-z0-9]/i', '_', $string);
    $string = strtolower($string);
    return $string;
}

/**
 * Verifica se uma string é um CPF válido
 * @param string $cpf CPF a ser verificado
 * @return bool True se o CPF for válido, False caso contrário
 */
function validarCPF($cpf) {
    // Remove caracteres não numéricos
    $cpf = preg_replace('/[^0-9]/', '', $cpf);
    
    // Verifica se o CPF tem 11 dígitos
    if (strlen($cpf) != 11) {
        return false;
    }
    
    // Verifica se todos os dígitos são iguais
    if (preg_match('/^(\d)\1+$/', $cpf)) {
        return false;
    }
    
    // Calcula o primeiro dígito verificador
    $soma = 0;
    for ($i = 0; $i < 9; $i++) {
        $soma += $cpf[$i] * (10 - $i);
    }
    $resto = $soma % 11;
    $dv1 = ($resto < 2) ? 0 : 11 - $resto;
    
    // Calcula o segundo dígito verificador
    $soma = 0;
    for ($i = 0; $i < 9; $i++) {
        $soma += $cpf[$i] * (11 - $i);
    }
    $soma += $dv1 * 2;
    $resto = $soma % 11;
    $dv2 = ($resto < 2) ? 0 : 11 - $resto;
    
    // Verifica se os dígitos verificadores estão corretos
    return ($cpf[9] == $dv1 && $cpf[10] == $dv2);
}

/**
 * Gera um token aleatório
 * @param int $tamanho Tamanho do token
 * @return string Token gerado
 */
function gerarToken($tamanho = 32) {
    return bin2hex(random_bytes($tamanho / 2));
}

/**
 * Verifica se uma data é válida
 * @param string $data Data no formato YYYY-MM-DD
 * @return bool True se a data for válida, False caso contrário
 */
function validarData($data) {
    $d = DateTime::createFromFormat('Y-m-d', $data);
    return $d && $d->format('Y-m-d') === $data;
}

/**
 * Calcula a diferença entre duas datas em dias
 * @param string $data1 Primeira data no formato YYYY-MM-DD
 * @param string $data2 Segunda data no formato YYYY-MM-DD
 * @return int Diferença em dias
 */
function diferencaDias($data1, $data2) {
    $d1 = new DateTime($data1);
    $d2 = new DateTime($data2);
    $diff = $d1->diff($d2);
    return $diff->days;
}

/**
 * Verifica se um evento já ocorreu
 * @param string $data Data do evento no formato YYYY-MM-DD
 * @return bool True se o evento já ocorreu, False caso contrário
 */
function eventoJaOcorreu($data) {
    $hoje = date('Y-m-d');
    return $data < $hoje;
}

/**
 * Trunca um texto para um tamanho máximo
 * @param string $texto Texto a ser truncado
 * @param int $tamanho Tamanho máximo
 * @param string $sufixo Sufixo a ser adicionado quando o texto for truncado
 * @return string Texto truncado
 */
function truncarTexto($texto, $tamanho = 100, $sufixo = '...') {
    if (strlen($texto) <= $tamanho) {
        return $texto;
    }
    return substr($texto, 0, $tamanho) . $sufixo;
}
?>