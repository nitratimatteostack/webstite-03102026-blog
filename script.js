const tabs = document.querySelectorAll('.tab');
const links = document.querySelectorAll('[data-tab]');
const menu = document.getElementById('menu');
const menuBtn = document.getElementById('menuBtn');

function showTab(id) {
  if (!document.getElementById(id)) id = 'chi-sono';
  tabs.forEach(t => t.classList.toggle('active', t.id === id));
  links.forEach(l => l.classList.toggle('active', l.dataset.tab === id && l.closest('.menu')));
  window.scrollTo({ top: 0, behavior: 'smooth' });
  menu.classList.remove('open');
  menuBtn.classList.remove('open');
}

links.forEach(l => l.addEventListener('click', e => {
  e.preventDefault();
  const id = l.dataset.tab;
  history.pushState(null, '', '#' + id);
  showTab(id);
}));

window.addEventListener('popstate', () => showTab(location.hash.slice(1)));

menuBtn.addEventListener('click', () => {
  menu.classList.toggle('open');
  menuBtn.classList.toggle('open');
});

document.getElementById('year').textContent = new Date().getFullYear();

showTab(location.hash.slice(1) || 'chi-sono');
