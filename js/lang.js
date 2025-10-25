// Simple i18n for header/navigation and basic page lang handling
(function() {
  var LANG_KEY = 'lang';

  var translations = {
    pt: {
      // Navegação
      'nav.home': 'Início',
      'nav.events': 'Eventos',
      'nav.my_tickets': 'Meus Ingressos',
      'nav.my_orders': 'Meus Pedidos',
      'nav.my_profile': 'Meu Perfil',
      'nav.logout': 'Sair',
      'nav.my_events': 'Meus Eventos',
      'nav.create_event': 'Criar Evento',
      'nav.validate_tickets': 'Validar Ingressos',
      'nav.plans': 'Planos',
      'nav.users': 'Usuários',
      'nav.reports': 'Relatórios',
      'nav.login': 'Login',
      'nav.signup': 'Cadastro',

      // Títulos
      'h.login': 'Login',
      'h.signup': 'Cadastro de Usuário',
      'h.client_dashboard': 'Painel do Cliente',
      'h.organizer_dashboard': 'Painel do Organizador',
      'h.admin_dashboard': 'Painel Administrativo',
      'h.available_events': 'Eventos Disponíveis',
      'h.manage_events': 'Gerenciar Eventos',
      'h.create_event': 'Criar Novo Evento',
      'h.edit_event': 'Editar Evento',
      'h.payment': 'Finalizar Pagamento',
      'h.my_tickets_title': 'Meus Ingressos',
      'h.transfer_ticket': 'Transferir Ingresso',
'h.user_profile': 'Meu Perfil',
      'h.upcoming_events': 'Próximos Eventos',
      'h.recent_orders': 'Meus Pedidos Recentes',
      'h.event_preferences': 'Preferências de Eventos',
      'msg.select_event_types': 'Selecione os tipos de eventos que você tem interesse:',
      'btn.select_all': 'Selecionar todos',
      'btn.clear': 'Limpar',
      'btn.save_preferences': 'Salvar Preferências',
'h.sales_monitor': 'Monitoramento de Vendas',
      'cta.view_sales_reports': 'Ver relatórios de vendas',
'h.event_details': 'Detalhes do Evento',
      'h.validate_tickets': 'Validar Ingressos',
      'h.scan_code': 'Digite ou escaneie o código do ingresso',
      'h.ticket_info': 'Informações do Ingresso',

      // Tabela (gerenciar eventos)
      'th.name': 'Nome',
      'th.date': 'Data',
      'th.start_time': 'Horário Início',
      'th.end_time': 'Horário Fim',
      'th.place': 'Local',
      'th.status': 'Status',
      'th.organizer': 'Organizador',
      'th.actions': 'Ações',

      // Mensagens comuns
      'msg.welcome_prefix': 'Bem-vindo(a), ',
      'msg.no_events_found': 'Nenhum evento encontrado com os critérios de busca.',
      'msg.no_events_available': 'Não há eventos disponíveis no momento.',
      'msg.no_tickets_yet': 'Você ainda não possui ingressos.',
      'msg.no_orders_yet': 'Você ainda não realizou nenhum pedido.',

      // Botões
      'btn.login': 'Entrar',
      'btn.signup': 'Cadastrar',
      'btn.submit': 'Enviar',
      'btn.cancel': 'Cancelar',
      'btn.search': 'Buscar',
      'btn.clear_filters': 'Limpar Filtros',
      'btn.view_details': 'Ver Detalhes',
      'btn.download': 'Download',
      'btn.transfer': 'Transferir Ingresso',
      'btn.save': 'Salvar',
      'btn.create_event': 'Criar Evento',
'btn.see_all_events': 'Ver todos os eventos',
      'btn.see_all_tickets': 'Ver todos os ingressos',
      'btn.see_all_orders': 'Ver todos os pedidos',
      'btn.edit': 'Editar',
      'btn.tickets': 'Ingressos',
      'btn.delete': 'Excluir',
      'btn.validate_ticket': 'Validar Ingresso',
      'btn.mark_as_used': 'Marcar como USADO',

      // Labels
      'label.email': 'E-mail:',
      'label.password': 'Senha:',
      'label.confirm_password': 'Confirmar Senha:',
      'label.name': 'Nome Completo:',
      'label.cpf': 'CPF:',
      'label.city': 'Cidade:',
      'label.district': 'Bairro:',
      'label.street': 'Rua:',
      'label.number': 'Número:',
      'label.date': 'Data:',
      'label.start_time': 'Horário de Início:',
      'label.end_time': 'Horário de Encerramento:',
      'label.status': 'Status:',
      'label.event_name': 'Nome do Evento:',
      'label.description': 'Descrição:',
      'label.location': 'Local:',
      'label.search_event_name': 'Nome do evento:',
      'label.code': 'Código:',
      'label.event': 'Evento:',
      'label.time': 'Horário:',
      'label.ticket_type': 'Tipo de Ingresso:',
      'label.value': 'Valor:',
      'label.customer_name': 'Nome do Cliente:',
      'label.customer_email': 'Email do Cliente:',
      'label.purchase_date': 'Data de Aquisição:',
      'label.usage_date': 'Data de Uso:',
      'label.organized_by': 'Organizado por:',

      // Placeholders
'ph.filter_event_types': 'Filtrar tipos de eventos...',
      'ph.ticket_code': 'Digite o código do ingresso',

      // Pagamento
      'h.order_summary': 'Resumo do Pedido',
      'h.choose_payment_method': 'Escolha o método de pagamento',
      'pm.credit_card': 'Cartão de Crédito',
      'pm.debit_card': 'Cartão de Débito',
      'pm.pix': 'PIX',
      'pm.boleto': 'Boleto Bancário',
'btn.finish_payment': 'Finalizar Pagamento',
      'btn.current_plan': 'Plano Atual',
      'btn.select_plan': 'Selecionar Plano',
      'btn.subscribe_gold': 'Assinar Plano Gold',
      'btn.pay_with_pix': 'Pagar com PIX',
      'btn.card': 'Cartão',
      'label.total': 'Total:',
      'label.per_month': '/mês',
      'h.plan_management': 'Gerenciamento de Planos',
      'h.choose_plan': 'Escolha o Plano Ideal para Você',
      'label.current_plan': 'Seu Plano Atual:',
      'plan.normal': 'Plano Normal',
      'plan.gold': 'Plano Gold',
      'feat.access_all_events': 'Acesso a todos os eventos',
      'feat.buy_tickets': 'Compra de ingressos',
      'feat.create_1_week': 'Criação de 1 evento por semana',
      'feat.advanced_features': 'Recursos avançados',
      'feat.create_3_week': 'Criação de 3 eventos por semana',
      'feat.featured_events': 'Destaque nos eventos',
      'feat.priority_support': 'Suporte prioritário',
      'h.note': 'Nota:',
      'msg.demo_note': 'Esta é uma página de demonstração. Em um ambiente de produção, o usuário seria redirecionado para um gateway de pagamento seguro para processar a transação.',
      
        // Páginas específicas
        'h.my_tickets': 'Meus Ingressos',
        'h.transfer_ticket': 'Transferir Ingresso',
        'h.download_ticket': 'Baixar Ingresso',
        'h.ticket_details': 'Detalhes do Ingresso',
        'h.event_info': 'Informações do Evento',
        'h.your_tickets': 'Seus Ingressos',
        'h.view_event_details': 'Ver Detalhes do Evento',
        'h.tickets_generated': 'Ingressos Gerados',
        'h.tickets_generated_success': 'Seus Ingressos Foram Gerados!',
        'h.tickets': 'Ingressos:',
        'h.ticket_type': 'Ingresso',
        'h.manage_users': 'Gerenciar Usuários',
        'h.reports': 'Relatórios',
        'h.reports_and_statistics': 'Relatórios e Estatísticas',
        'h.total_revenue': 'Receita Total',
        'h.events_by_status': 'Eventos por Status',
        'h.users_by_type': 'Usuários por Tipo',
        'h.total_tickets_sold': 'Total de Ingressos Vendidos',
        'h.total_sales_value': 'Valor Total de Vendas',
        'h.active_events': 'Eventos Ativos',
        'h.sales_evolution': 'Evolução de Vendas',
        'h.sales_summary_by_event': 'Resumo de Vendas por Evento',
        'h.forgot_password': 'Esqueci Minha Senha',
        'h.reset_password': 'Redefinir Senha',
      
        // Labels específicos
        'label.date': 'Data:',
        'label.time': 'Horário:',
        'label.location': 'Local:',
        'label.code': 'Código:',
        'label.value': 'Valor:',
        'label.purchase_date': 'Data de Aquisição:',
        'label.usage_date': 'Data de Uso:',
        'label.ticket_type': 'Tipo de Ingresso:',
        'label.status': 'Status:',
        'label.simulated_qr_code': 'Código QR simulado',
        'label.period': 'Período:',
        'label.registered_email': 'E-mail cadastrado:',
        'label.new_password': 'Nova Senha:',
        'label.confirm_new_password': 'Confirmar Nova Senha:',
      
        // Mensagens específicas
        'msg.no_tickets_yet': 'Você ainda não possui ingressos',
        'msg.explore_events': 'Explore os eventos disponíveis e adquira seus ingressos!',
        'msg.ticket_not_found': 'Ingresso não encontrado ou não está disponível para transferência.',
        'msg.ticket_transferred': 'Ingresso transferido com sucesso para',
        'msg.cannot_transfer_to_self': 'Você não pode transferir um ingresso para você mesmo.',
        'msg.email_not_registered': 'O e-mail informado não está cadastrado no sistema.',
        'msg.enter_recipient_email': 'Por favor, informe o e-mail do destinatário.',
        'msg.ticket_transfer_error': 'Erro ao transferir o ingresso:',
        'msg.purchase_successful': 'Compra realizada com sucesso!',
        'msg.no_users_found': 'Nenhum usuário encontrado.',
        'msg.current_user': 'Usuário atual',
        'msg.registered_in_system': 'Cadastrados no sistema',
        'msg.events_created': 'Eventos criados',
        'msg.total_sales': 'Total de vendas',
        'msg.amount_raised': 'Valor arrecadado',
        'msg.no_events_found': 'Nenhum evento encontrado.',
        'msg.no_sales_in_period': 'Nenhuma venda registrada no período selecionado.',
      
        // Botões específicos
        'btn.transfer': 'Transferir',
        'btn.download': 'Baixar Ingresso',
        'btn.view_details': 'Ver Detalhes do Evento',
        'btn.transfer_ticket': 'Transferir Ingresso',
        'btn.cancel': 'Cancelar',
        'btn.filter': 'Filtrar',
        'btn.clear': 'Limpar',
        'btn.activate': 'Ativar',
        'btn.deactivate': 'Desativar',
        'btn.send_reset_link': 'Enviar Link de Redefinição',
        'btn.back_to_login': 'Voltar para o login',
        'btn.reset_password': 'Redefinir Senha',
      
        // Status
        'status.active': 'ATIVO',
        'status.used': 'USADO',
        'status.cancelled': 'CANCELADO',
        'status.confirmed': 'CONFIRMADO',
        'status.postponed': 'ADIADO',
        'status.inactive': 'INATIVO',
      
      // Labels adicionais
      'label.start_time': 'Horário de início:',
      'label.end_time': 'Horário de encerramento:',
      'label.address': 'Endereço:',
      'label.organizer': 'Organizador:',
      'label.available': 'Disponíveis:',
      'label.quantity': 'Quantidade:',
      'label.event': 'Evento:',
      'label.customer_name': 'Nome do Cliente:',
      'label.customer_email': 'Email do Cliente:',
      'label.search_event_name': 'Nome do evento:',
      
      // Títulos adicionais
      'h.description': 'Descrição',
      'h.available_tickets': 'Ingressos Disponíveis',
      
      // Mensagens adicionais
      'msg.login_to_buy': 'Para comprar ingressos, você precisa fazer login como cliente.',
      'msg.no_tickets_available': 'Não há ingressos disponíveis para este evento.',
      
      // Botões adicionais
      'btn.buy_tickets': 'Comprar Ingressos',
      'btn.back_to_events': 'Voltar para Eventos',
      'btn.mark_as_used': 'Marcar como USADO',
      'btn.validate_ticket': 'Validar Ingresso',
      'btn.clear_filters': 'Limpar Filtros',
      'btn.see_all_events': 'Ver todos os eventos',
      'btn.see_all_tickets': 'Ver todos os ingressos',
      'btn.see_all_orders': 'Ver todos os pedidos',
      
      // Placeholders
      'ph.ticket_code': 'Digite o código do ingresso',
      
      // Mensagens adicionais
      'msg.manage_events_description': 'Gerencie seus eventos e ingressos.',
      'msg.sales_monitor_description': 'Acompanhe as vendas de ingressos e estatísticas dos seus eventos.',
      'msg.create_event_description': 'Adicione um novo evento à plataforma.',
      
      // Botões adicionais
      'btn.manage_events': 'Gerenciar Eventos',
      'btn.view_sales_report': 'Ver Relatório de Vendas',
      
      // Títulos adicionais
      'h.total_users': 'Total de Usuários',
      'h.total_events': 'Total de Eventos',
      'h.tickets_sold': 'Ingressos Vendidos',
      'h.recent_events': 'Eventos Recentes',
      'h.recent_users': 'Usuários Recentes',
      
      // Mensagens adicionais
      'msg.no_events_registered': 'Nenhum evento cadastrado.',
      'msg.no_users_registered': 'Nenhum usuário cadastrado.',
      
      // Botões adicionais
      'btn.see_all': 'Ver Todos',
      'btn.see_reports': 'Ver Relatórios',
      
      // Labels adicionais
      'label.type': 'Tipo:',
      
      // Navegação adicional
      'nav.settings': 'Configurações',
      
      // Cabeçalhos de tabela
      'th.name': 'Nome',
      'th.date': 'Data',
      'th.start_time': 'Horário Início',
      'th.end_time': 'Horário Fim',
      'th.location': 'Local',
      'th.status': 'Status',
      'th.organizer': 'Organizador',
      'th.actions': 'Ações',
      
      // Botões adicionais
      'btn.create_first_event': 'Criar Primeiro Evento',
      
      // Labels adicionais
      'label.event_name': 'Nome do Evento:',
      'label.description': 'Descrição:',
      'label.city': 'Cidade:',
      'label.district': 'Bairro:',
      'label.street': 'Rua:',
      'label.number': 'Número:',
      'label.image_optional': 'Imagem do Local (opcional):',
      'label.current_image': 'Imagem atual:',
      
      // Mensagens adicionais
      'msg.image_formats': 'Formatos aceitos: JPG e PNG. Tamanho máximo: 2MB.',
      
      // Botões adicionais
      'btn.save_changes': 'Salvar Alterações',
      
      // Títulos adicionais
      'h.delete_event': 'Excluir Evento',
      'h.warning': 'Atenção!',
      
      // Mensagens adicionais
      'msg.delete_warning': 'Você está prestes a excluir o evento',
      'msg.delete_irreversible': 'Esta ação não poderá ser desfeita. Todos os ingressos não vendidos associados a este evento também serão excluídos.',
      
      // Botões adicionais
      'btn.confirm_deletion': 'Confirmar Exclusão',
      
      // Títulos adicionais
      'h.manage_tickets': 'Gerenciar Ingressos',
      'h.add_tickets': 'Adicionar Ingressos',
      
      // Labels adicionais
      'label.price': 'Preço:',
      'label.sold': 'Vendidos:',
      'label.total': 'Total:',
      
      // Mensagens adicionais
      'msg.no_tickets_registered': 'Nenhum ingresso cadastrado para este evento.',
      
      // Botões adicionais
      'btn.add_tickets': 'Adicionar Ingressos',
      'btn.delete_available': 'Excluir Disponíveis'
    },
    en: {
      // Navigation
      'nav.home': 'Home',
      'nav.events': 'Events',
      'nav.my_tickets': 'My Tickets',
      'nav.my_orders': 'My Orders',
      'nav.my_profile': 'My Profile',
      'nav.logout': 'Logout',
      'nav.my_events': 'My Events',
      'nav.create_event': 'Create Event',
      'nav.validate_tickets': 'Validate Tickets',
      'nav.plans': 'Plans',
      'nav.users': 'Users',
      'nav.reports': 'Reports',
      'nav.login': 'Login',
      'nav.signup': 'Sign Up',

      // Headings
      'h.login': 'Login',
      'h.signup': 'User Registration',
      'h.client_dashboard': 'Client Dashboard',
      'h.organizer_dashboard': 'Organizer Dashboard',
      'h.admin_dashboard': 'Admin Dashboard',
      'h.available_events': 'Available Events',
      'h.manage_events': 'Manage Events',
      'h.create_event': 'Create New Event',
      'h.edit_event': 'Edit Event',
      'h.payment': 'Checkout',
      'h.my_tickets_title': 'My Tickets',
      'h.transfer_ticket': 'Transfer Ticket',
'h.user_profile': 'My Profile',
      'h.upcoming_events': 'Upcoming Events',
      'h.recent_orders': 'My Recent Orders',
      'h.event_preferences': 'Event Preferences',
      'msg.select_event_types': 'Select the event types you are interested in:',
      'btn.select_all': 'Select all',
      'btn.clear': 'Clear',
      'btn.save_preferences': 'Save Preferences',
'h.sales_monitor': 'Sales Monitoring',
      'cta.view_sales_reports': 'View sales reports',
'h.event_details': 'Event Details',
      'h.validate_tickets': 'Validate Tickets',
      'h.scan_code': 'Enter or scan ticket code',
      'h.ticket_info': 'Ticket Information',

      // Table (manage events)
      'th.name': 'Name',
      'th.date': 'Date',
      'th.start_time': 'Start Time',
      'th.end_time': 'End Time',
      'th.place': 'Location',
      'th.status': 'Status',
      'th.organizer': 'Organizer',
      'th.actions': 'Actions',

      // Common messages
      'msg.welcome_prefix': 'Welcome, ',
      'msg.no_events_found': 'No events found for the selected filters.',
      'msg.no_events_available': 'No events available at the moment.',
      'msg.no_tickets_yet': 'You do not have tickets yet.',
      'msg.no_orders_yet': 'You have not placed any orders yet.',

      // Buttons
      'btn.login': 'Login',
      'btn.signup': 'Sign Up',
      'btn.submit': 'Submit',
      'btn.cancel': 'Cancel',
      'btn.search': 'Search',
      'btn.clear_filters': 'Clear Filters',
      'btn.view_details': 'View Details',
      'btn.download': 'Download',
      'btn.transfer': 'Transfer Ticket',
      'btn.save': 'Save',
      'btn.create_event': 'Create Event',
'btn.see_all_events': 'See all events',
      'btn.see_all_tickets': 'See all tickets',
      'btn.see_all_orders': 'See all orders',
      'btn.edit': 'Edit',
      'btn.tickets': 'Tickets',
      'btn.delete': 'Delete',
      'btn.validate_ticket': 'Validate Ticket',
      'btn.mark_as_used': 'Mark as USED',

      // Labels
      'label.email': 'Email:',
      'label.password': 'Password:',
      'label.confirm_password': 'Confirm Password:',
      'label.name': 'Full Name:',
      'label.cpf': 'CPF:',
      'label.city': 'City:',
      'label.district': 'District:',
      'label.street': 'Street:',
      'label.number': 'Number:',
      'label.date': 'Date:',
      'label.start_time': 'Start Time:',
      'label.end_time': 'End Time:',
      'label.status': 'Status:',
      'label.event_name': 'Event Name:',
      'label.description': 'Description:',
      'label.location': 'Location:',
      'label.search_event_name': 'Event name:',
      'label.code': 'Code:',
      'label.event': 'Event:',
      'label.time': 'Time:',
      'label.ticket_type': 'Ticket Type:',
      'label.value': 'Value:',
      'label.customer_name': 'Customer Name:',
      'label.customer_email': 'Customer Email:',
      'label.purchase_date': 'Purchase Date:',
      'label.usage_date': 'Usage Date:',
      'label.organized_by': 'Organized by:',

      // Placeholders
'ph.filter_event_types': 'Filter event types...',
      'ph.ticket_code': 'Enter ticket code',

      // Payment
      'h.order_summary': 'Order Summary',
      'h.choose_payment_method': 'Choose payment method',
      'pm.credit_card': 'Credit Card',
      'pm.debit_card': 'Debit Card',
      'pm.pix': 'PIX',
      'pm.boleto': 'Bank Slip',
'btn.finish_payment': 'Complete Payment',
      'btn.current_plan': 'Current Plan',
      'btn.select_plan': 'Select Plan',
      'btn.subscribe_gold': 'Subscribe to Gold Plan',
      'btn.pay_with_pix': 'Pay with PIX',
      'btn.card': 'Card',
      'label.total': 'Total:',
      'label.per_month': '/month',
      'h.plan_management': 'Plan Management',
      'h.choose_plan': 'Choose the Ideal Plan for You',
      'label.current_plan': 'Your Current Plan:',
      'plan.normal': 'Normal Plan',
      'plan.gold': 'Gold Plan',
      'feat.access_all_events': 'Access all events',
      'feat.buy_tickets': 'Ticket purchase',
      'feat.create_1_week': 'Create 1 event per week',
      'feat.advanced_features': 'Advanced features',
      'feat.create_3_week': 'Create 3 events per week',
      'feat.featured_events': 'Featured in events',
      'feat.priority_support': 'Priority support',
      'h.note': 'Note:',
      'msg.demo_note': 'This is a demo page. In production, the user would be redirected to a secure payment gateway to process the transaction.',
      
        // Páginas específicas
        'h.my_tickets': 'My Tickets',
        'h.transfer_ticket': 'Transfer Ticket',
        'h.download_ticket': 'Download Ticket',
        'h.ticket_details': 'Ticket Details',
        'h.event_info': 'Event Information',
        'h.your_tickets': 'Your Tickets',
        'h.view_event_details': 'View Event Details',
        'h.tickets_generated': 'Tickets Generated',
        'h.tickets_generated_success': 'Your Tickets Have Been Generated!',
        'h.tickets': 'Tickets:',
        'h.ticket_type': 'Ticket',
        'h.manage_users': 'Manage Users',
        'h.reports': 'Reports',
        'h.reports_and_statistics': 'Reports and Statistics',
        'h.total_revenue': 'Total Revenue',
        'h.events_by_status': 'Events by Status',
        'h.users_by_type': 'Users by Type',
        'h.total_tickets_sold': 'Total Tickets Sold',
        'h.total_sales_value': 'Total Sales Value',
        'h.active_events': 'Active Events',
        'h.sales_evolution': 'Sales Evolution',
        'h.sales_summary_by_event': 'Sales Summary by Event',
        'h.forgot_password': 'Forgot Password',
        'h.reset_password': 'Reset Password',
      
        // Labels específicos
        'label.date': 'Date:',
        'label.time': 'Time:',
        'label.location': 'Location:',
        'label.code': 'Code:',
        'label.value': 'Value:',
        'label.purchase_date': 'Purchase Date:',
        'label.usage_date': 'Usage Date:',
        'label.ticket_type': 'Ticket Type:',
        'label.status': 'Status:',
        'label.simulated_qr_code': 'Simulated QR Code',
        'label.period': 'Period:',
        'label.registered_email': 'Registered email:',
        'label.new_password': 'New Password:',
        'label.confirm_new_password': 'Confirm New Password:',
      
        // Mensagens específicas
        'msg.no_tickets_yet': 'You do not have tickets yet',
        'msg.explore_events': 'Explore available events and purchase your tickets!',
        'msg.ticket_not_found': 'Ticket not found or not available for transfer.',
        'msg.ticket_transferred': 'Ticket successfully transferred to',
        'msg.cannot_transfer_to_self': 'You cannot transfer a ticket to yourself.',
        'msg.email_not_registered': 'The provided email is not registered in the system.',
        'msg.enter_recipient_email': 'Please enter the recipient email.',
        'msg.ticket_transfer_error': 'Error transferring ticket:',
        'msg.purchase_successful': 'Purchase completed successfully!',
        'msg.no_users_found': 'No users found.',
        'msg.current_user': 'Current user',
        'msg.registered_in_system': 'Registered in system',
        'msg.events_created': 'Events created',
        'msg.total_sales': 'Total sales',
        'msg.amount_raised': 'Amount raised',
        'msg.no_events_found': 'No events found.',
        'msg.no_sales_in_period': 'No sales recorded in the selected period.',
      
        // Botões específicos
        'btn.transfer': 'Transfer',
        'btn.download': 'Download Ticket',
        'btn.view_details': 'View Event Details',
        'btn.transfer_ticket': 'Transfer Ticket',
        'btn.cancel': 'Cancel',
        'btn.filter': 'Filter',
        'btn.clear': 'Clear',
        'btn.activate': 'Activate',
        'btn.deactivate': 'Deactivate',
        'btn.send_reset_link': 'Send Reset Link',
        'btn.back_to_login': 'Back to Login',
        'btn.reset_password': 'Reset Password',
      
        // Status
        'status.active': 'ACTIVE',
        'status.used': 'USED',
        'status.cancelled': 'CANCELLED',
        'status.confirmed': 'CONFIRMED',
        'status.postponed': 'POSTPONED',
        'status.inactive': 'INACTIVE',
      
      // Labels adicionais
      'label.start_time': 'Start Time:',
      'label.end_time': 'End Time:',
      'label.address': 'Address:',
      'label.organizer': 'Organizer:',
      'label.available': 'Available:',
      'label.quantity': 'Quantity:',
      'label.event': 'Event:',
      'label.customer_name': 'Customer Name:',
      'label.customer_email': 'Customer Email:',
      'label.search_event_name': 'Event name:',
      
      // Títulos adicionais
      'h.description': 'Description',
      'h.available_tickets': 'Available Tickets',
      
      // Mensagens adicionais
      'msg.login_to_buy': 'To buy tickets, you need to login as a client.',
      'msg.no_tickets_available': 'No tickets available for this event.',
      
      // Botões adicionais
      'btn.buy_tickets': 'Buy Tickets',
      'btn.back_to_events': 'Back to Events',
      'btn.mark_as_used': 'Mark as USED',
      'btn.validate_ticket': 'Validate Ticket',
      'btn.clear_filters': 'Clear Filters',
      'btn.see_all_events': 'See all events',
      'btn.see_all_tickets': 'See all tickets',
      'btn.see_all_orders': 'See all orders',
      
      // Placeholders
      'ph.ticket_code': 'Enter ticket code',
      
      // Mensagens adicionais
      'msg.manage_events_description': 'Manage your events and tickets.',
      'msg.sales_monitor_description': 'Track ticket sales and statistics for your events.',
      'msg.create_event_description': 'Add a new event to the platform.',
      
      // Botões adicionais
      'btn.manage_events': 'Manage Events',
      'btn.view_sales_report': 'View Sales Report',
      
      // Títulos adicionais
      'h.total_users': 'Total Users',
      'h.total_events': 'Total Events',
      'h.tickets_sold': 'Tickets Sold',
      'h.recent_events': 'Recent Events',
      'h.recent_users': 'Recent Users',
      
      // Mensagens adicionais
      'msg.no_events_registered': 'No events registered.',
      'msg.no_users_registered': 'No users registered.',
      
      // Botões adicionais
      'btn.see_all': 'See All',
      'btn.see_reports': 'See Reports',
      
      // Labels adicionais
      'label.type': 'Type:',
      
      // Navegação adicional
      'nav.settings': 'Settings',
      
      // Cabeçalhos de tabela
      'th.name': 'Name',
      'th.date': 'Date',
      'th.start_time': 'Start Time',
      'th.end_time': 'End Time',
      'th.location': 'Location',
      'th.status': 'Status',
      'th.organizer': 'Organizer',
      'th.actions': 'Actions',
      
      // Botões adicionais
      'btn.create_first_event': 'Create First Event',
      
      // Labels adicionais
      'label.event_name': 'Event Name:',
      'label.description': 'Description:',
      'label.city': 'City:',
      'label.district': 'District:',
      'label.street': 'Street:',
      'label.number': 'Number:',
      'label.image_optional': 'Location Image (optional):',
      'label.current_image': 'Current image:',
      
      // Mensagens adicionais
      'msg.image_formats': 'Accepted formats: JPG and PNG. Maximum size: 2MB.',
      
      // Botões adicionais
      'btn.save_changes': 'Save Changes',
      
      // Títulos adicionais
      'h.delete_event': 'Delete Event',
      'h.warning': 'Warning!',
      
      // Mensagens adicionais
      'msg.delete_warning': 'You are about to delete the event',
      'msg.delete_irreversible': 'This action cannot be undone. All unsold tickets associated with this event will also be deleted.',
      
      // Botões adicionais
      'btn.confirm_deletion': 'Confirm Deletion',
      
      // Títulos adicionais
      'h.manage_tickets': 'Manage Tickets',
      'h.add_tickets': 'Add Tickets',
      
      // Labels adicionais
      'label.price': 'Price:',
      'label.sold': 'Sold:',
      'label.total': 'Total:',
      
      // Mensagens adicionais
      'msg.no_tickets_registered': 'No tickets registered for this event.',
      
      // Botões adicionais
      'btn.add_tickets': 'Add Tickets',
      'btn.delete_available': 'Delete Available'
    }
  };

  function saveLang(lang) {
    try { localStorage.setItem(LANG_KEY, lang); } catch (e) {}
  }

  function getLang() {
    var l = 'pt';
    try { l = localStorage.getItem(LANG_KEY) || 'pt'; } catch (e) {}
    return l === 'en' ? 'en' : 'pt';
  }

  function setDocumentLang(lang) {
    document.documentElement.setAttribute('lang', lang === 'en' ? 'en' : 'pt-BR');
  }

  function dictFor(lang) {
    return translations[lang] || translations.pt;
  }

  // Helpers to set button labels without removing child icon elements
  function setButtonLabel(el, text) {
    if (!el) return;
    var replaced = false;
    for (var i = 0; i < el.childNodes.length; i++) {
      var n = el.childNodes[i];
      if (n.nodeType === Node.TEXT_NODE && n.nodeValue.trim() !== '') {
        n.nodeValue = text;
        replaced = true;
        break;
      }
    }
    if (!replaced) {
      // If no existing text node, append one
      el.appendChild(document.createTextNode(text));
    }
  }

  function buildSwapMaps() {
    var pt = translations.pt || {};
    var en = translations.en || {};
    var ptToEn = {};
    var enToPt = {};
    Object.keys(pt).forEach(function(key) {
      if (en[key]) {
        ptToEn[pt[key]] = en[key];
        enToPt[en[key]] = pt[key];
      }
    });

    // Dynamic terms commonly found in event types/preferences
    var dynamicTermPairs = [
      ['Música', 'Music'],
      ['Show', 'Show'],
      ['Teatro', 'Theater'],
      ['Esportes', 'Sports'],
      ['Futebol', 'Soccer'],
      ['Basquete', 'Basketball'],
      ['Vôlei', 'Volleyball'],
      ['Conferência', 'Conference'],
      ['Palestra', 'Talk'],
      ['Workshop', 'Workshop'],
      ['Feira', 'Fair'],
      ['Festival', 'Festival'],
      ['Tecnologia', 'Technology'],
      ['Negócios', 'Business'],
      ['Educação', 'Education'],
      ['Artes', 'Arts'],
      ['Cinema', 'Cinema'],
      ['Dança', 'Dance'],
      ['Gastronomia', 'Gastronomy'],
      ['Comédia', 'Comedy'],
      ['Religião', 'Religion'],
      ['Saúde', 'Health'],
      ['Bem-estar', 'Wellness'],
      ['Infantil', 'Kids'],
      ['Família', 'Family'],
      ['Networking', 'Networking'],
      // Page intro/cta sentences
      ['Meus Eventos', 'My Events'],
      ['Gerencie seus eventos e ingressos.', 'Manage your events and tickets.'],
      ['Gerencie seus eventos e ingressos', 'Manage your events and tickets'],
      ['Acompanhe as vendas de ingressos e estatísticas dos seus eventos.', 'Track ticket sales and event statistics.'],
      ['Acompanhe as vendas de ingressos e estatísticas dos seus eventos', 'Track ticket sales and event statistics'],
      ['Adicione um novo evento à plataforma.', 'Add a new event to the platform.'],
      ['Adicione um novo evento à plataforma', 'Add a new event to the platform'],
      ['Ver relatórios de vendas', 'View sales reports'],
      ['Ver relatorios de vendas', 'View sales reports']
    ];
    dynamicTermPairs.forEach(function(pair) {
      var a = pair[0], b = pair[1];
      if (a && b) {
        ptToEn[a] = b;
        enToPt[b] = a;
      }
    });

    return { ptToEn: ptToEn, enToPt: enToPt };
  }

  function translateTextNodesIn(rootEl, lang) {
    if (!rootEl) return;
    var maps = buildSwapMaps();
    var map = lang === 'en' ? maps.ptToEn : maps.enToPt;
    var walker = document.createTreeWalker(rootEl, NodeFilter.SHOW_TEXT, {
      acceptNode: function(node) {
        if (!node.nodeValue) return NodeFilter.FILTER_REJECT;
        // Ignore pure whitespace
        if (!node.nodeValue.trim()) return NodeFilter.FILTER_REJECT;
        return NodeFilter.FILTER_ACCEPT;
      }
    });
    var textNode;
    while ((textNode = walker.nextNode())) {
      var text = textNode.nodeValue;
      // Replace all occurrences for each known phrase
      Object.keys(map).forEach(function(ptStr) {
        if (!ptStr) return;
        if (text.indexOf(ptStr) !== -1) {
          text = text.split(ptStr).join(map[ptStr]);
        }
      });
      textNode.nodeValue = text;
    }
  }

  function applyTranslations(lang) {
    var dict = dictFor(lang);
    // Text content
    document.querySelectorAll('[data-i18n]').forEach(function(el) {
      var key = el.getAttribute('data-i18n');
      var val = key && dict[key];
      if (!val) return;
      if (el.tagName === 'INPUT') {
        var type = (el.getAttribute('type') || '').toLowerCase();
        if (type === 'button' || type === 'submit' || type === 'reset') {
          el.value = val;
        }
      } else if (el.tagName === 'BUTTON') {
        setButtonLabel(el, val);
      } else {
        el.textContent = val;
      }
    });
    // Placeholder
    document.querySelectorAll('[data-i18n-placeholder]').forEach(function(el) {
      var key = el.getAttribute('data-i18n-placeholder');
      if (key && dict[key] && 'placeholder' in el) el.placeholder = dict[key];
    });
    // Title attribute
    document.querySelectorAll('[data-i18n-title]').forEach(function(el) {
      var key = el.getAttribute('data-i18n-title');
      if (key && dict[key]) el.title = dict[key];
    });
    // Aria-label
    document.querySelectorAll('[data-i18n-aria-label]').forEach(function(el) {
      var key = el.getAttribute('data-i18n-aria-label');
      if (key && dict[key]) el.setAttribute('aria-label', dict[key]);
    });

    // Value attribute (e.g., input[type=submit])
    document.querySelectorAll('[data-i18n-value]').forEach(function(el) {
      var key = el.getAttribute('data-i18n-value');
      if (key && dict[key]) el.value = dict[key];
    });

    // Scoped text replacements inside specific containers without data-i18n tags
    var SCOPES = [
      '.form-validacao',
      '.card.panel-container',
      '.alert.alert-info',
      '.card.panel-container.slide-in-left.fade-in',
      '.card.panel-container.slide-in-right.fade-in',
      '.eventos-table thead',
      '.dashboard-menu',
      '.welcome-message',
      '.pagamento-container',
      '.metodos-pagamento',
      '#listaPreferencias',
      '.ingresso-info'
    ];
    SCOPES.forEach(function(sel) {
      document.querySelectorAll(sel).forEach(function(container) {
        translateTextNodesIn(container, lang);
      });
    });
  }

  function updateToggleLabel(lang) {
    var label = document.getElementById('lang-toggle-label');
    if (label) label.textContent = lang === 'en' ? 'EN' : 'PT';
  }

  function initToggle() {
    var btn = document.getElementById('lang-toggle');
    if (!btn) return;
    btn.addEventListener('click', function() {
      var next = getLang() === 'en' ? 'pt' : 'en';
      saveLang(next);
      setDocumentLang(next);
      applyTranslations(next);
      updateToggleLabel(next);
    });
  }

  document.addEventListener('DOMContentLoaded', function() {
    var lang = getLang();
    setDocumentLang(lang);
    applyTranslations(lang);
    updateToggleLabel(lang);
    initToggle();
  });
})();
