// Mobile menu toggle
document.querySelectorAll('[data-nav-toggle]').forEach(function (btn) {
  btn.addEventListener('click', function () {
    var nav = document.querySelector('[data-nav]');
    var open = nav.classList.toggle('open');
    btn.setAttribute('aria-expanded', open ? 'true' : 'false');
  });
});

// Confirm before destructive actions: <form data-confirm="Are you sure?">
document.querySelectorAll('form[data-confirm]').forEach(function (f) {
  f.addEventListener('submit', function (ev) {
    if (!window.confirm(f.getAttribute('data-confirm'))) ev.preventDefault();
  });
});

// Live total on the booking form
var qty = document.querySelector('[data-qty]');
if (qty) {
  var price = parseFloat(qty.getAttribute('data-price'));
  var out = document.querySelector('[data-total]');
  var update = function () {
    var n = Math.max(0, parseInt(qty.value, 10) || 0);
    out.textContent = '₹' + (n * price).toLocaleString('en-IN');
  };
  qty.addEventListener('input', update);
  update();
}
