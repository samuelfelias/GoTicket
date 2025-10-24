<?php

namespace App\Pattern;

use App\Model\Usuario;

/**
 * Observer Pattern - Interface para observadores
 */
interface Observer
{
    public function update(string $mensagem, array $dados = []): void;
}

/**
 * Subject - Gerenciador de notificações
 */
class NotificationManager
{
    private array $observers = [];
    private array $historico = [];

    public function attach(Observer $observer): void
    {
        $this->observers[] = $observer;
    }

    public function detach(Observer $observer): void
    {
        $this->observers = array_filter(
            $this->observers,
            fn($obs) => $obs !== $observer
        );
    }

    public function notify(string $mensagem, array $dados = []): void
    {
        $this->historico[] = [
            'mensagem' => $mensagem,
            'dados' => $dados,
            'timestamp' => date('Y-m-d H:i:s')
        ];

        foreach ($this->observers as $observer) {
            $observer->update($mensagem, $dados);
        }
    }

    public function getHistorico(): array
    {
        return $this->historico;
    }
}

/**
 * Observer concreto - Notificação por Email
 */
class EmailObserver implements Observer
{
    private Usuario $usuario;
    private array $emailsEnviados = [];

    public function __construct(Usuario $usuario)
    {
        $this->usuario = $usuario;
    }

    public function update(string $mensagem, array $dados = []): void
    {
        $email = [
            'para' => $this->usuario->getEmail(),
            'assunto' => $this->gerarAssunto($dados),
            'mensagem' => $mensagem,
            'dados' => $dados,
            'enviado_em' => date('Y-m-d H:i:s')
        ];

        $this->emailsEnviados[] = $email;
        
        // Simula envio de email
        echo "\n📧 EMAIL enviado para {$this->usuario->getEmail()}\n";
        echo "   Assunto: {$email['assunto']}\n";
        echo "   Mensagem: {$mensagem}\n";
    }

    private function gerarAssunto(array $dados): string
    {
        if (isset($dados['tipo'])) {
            return match($dados['tipo']) {
                'novo_evento' => 'Novo evento disponível!',
                'promocao' => 'Promoção especial de ingressos!',
                'lembrete' => 'Lembrete: Seu evento está chegando',
                'confirmacao' => 'Confirmação de compra',
                default => 'Notificação GoTicket'
            };
        }
        return 'Notificação GoTicket';
    }

    public function getEmailsEnviados(): array
    {
        return $this->emailsEnviados;
    }
}

/**
 * Observer concreto - Notificação por SMS
 */
class SMSObserver implements Observer
{
    private Usuario $usuario;
    private array $smsEnviados = [];

    public function __construct(Usuario $usuario)
    {
        $this->usuario = $usuario;
    }

    public function update(string $mensagem, array $dados = []): void
    {
        // SMS deve ser curto
        $mensagemCurta = substr($mensagem, 0, 160);
        
        $sms = [
            'para' => $this->usuario->getTelefone(),
            'mensagem' => $mensagemCurta,
            'enviado_em' => date('Y-m-d H:i:s')
        ];

        $this->smsEnviados[] = $sms;
        
        // Simula envio de SMS
        echo "\n📱 SMS enviado para {$this->usuario->getTelefone()}\n";
        echo "   {$mensagemCurta}\n";
    }

    public function getSmsEnviados(): array
    {
        return $this->smsEnviados;
    }
}

/**
 * Observer concreto - Notificação Push
 */
class PushNotificationObserver implements Observer
{
    private Usuario $usuario;
    private array $notificacoesEnviadas = [];

    public function __construct(Usuario $usuario)
    {
        $this->usuario = $usuario;
    }

    public function update(string $mensagem, array $dados = []): void
    {
        $notificacao = [
            'usuario_id' => $this->usuario->getId(),
            'titulo' => $dados['titulo'] ?? 'GoTicket',
            'mensagem' => $mensagem,
            'dados' => $dados,
            'enviado_em' => date('Y-m-d H:i:s')
        ];

        $this->notificacoesEnviadas[] = $notificacao;
        
        // Simula notificação push
        echo "\n🔔 PUSH enviado para {$this->usuario->getNome()}\n";
        echo "   {$notificacao['titulo']}: {$mensagem}\n";
    }

    public function getNotificacoesEnviadas(): array
    {
        return $this->notificacoesEnviadas;
    }
}
