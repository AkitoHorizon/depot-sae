// Envoi du formulaire de contact en AJAX
fetch('contact_ajax.php', {
  method: 'POST',
  body: formData
});