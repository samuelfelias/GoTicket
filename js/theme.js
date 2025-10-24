// Theme handling: consistent, instant, cross-page, cross-tab
(function() {
  var THEME_KEY = 'theme';

  function setThemeClass(theme) {
    var root = document.documentElement;
    if (theme === 'dark') {
      root.classList.add('dark-theme');
    } else {
      root.classList.remove('dark-theme');
    }
  }

  function getSavedTheme() {
    try { return localStorage.getItem(THEME_KEY); } catch (e) { return null; }
  }

  function saveTheme(theme) {
    try { localStorage.setItem(THEME_KEY, theme); } catch (e) {}
  }

  function currentTheme() {
    return document.documentElement.classList.contains('dark-theme') ? 'dark' : 'light';
  }

  function updateToggleAccessibility(btn, theme) {
    if (!btn) return;
    var isDark = theme === 'dark';
    btn.setAttribute('aria-pressed', isDark ? 'true' : 'false');
    btn.title = isDark ? 'Alternar para tema claro' : 'Alternar para tema escuro';
  }

  function initThemeFromStorage() {
    var saved = getSavedTheme();
    if (saved === 'dark' || saved === 'light') setThemeClass(saved);
  }

  function initToggle() {
    var btn = document.getElementById('theme-toggle');
    if (!btn) return;
    updateToggleAccessibility(btn, currentTheme());
    btn.addEventListener('click', function() {
      var next = currentTheme() === 'dark' ? 'light' : 'dark';
      setThemeClass(next);
      saveTheme(next);
      updateToggleAccessibility(btn, next);
    });
  }

  // Sync across tabs
  window.addEventListener('storage', function(ev) {
    if (ev.key === THEME_KEY && (ev.newValue === 'dark' || ev.newValue === 'light')) {
      setThemeClass(ev.newValue);
      var btn = document.getElementById('theme-toggle');
      updateToggleAccessibility(btn, ev.newValue);
    }
  });

  // Apply as soon as possible after DOM ready
  document.addEventListener('DOMContentLoaded', function() {
    initThemeFromStorage();
    initToggle();
  });
})();


