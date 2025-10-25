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
      'msg.demo_note': 'Esta é uma página de demonstração. Em um ambiente de produção, o usuário seria redirecionado para um gateway de pagamento seguro para processar a transação.'
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
      'msg.demo_note': 'This is a demo page. In production, the user would be redirected to a secure payment gateway to process the transaction.'
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
