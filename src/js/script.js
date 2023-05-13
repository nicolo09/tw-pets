const IMG_DIR= "img/";

$.fn.select2.defaults.set( "theme", "bootstrap-5" );

// TODO: Update notification badge
if (/*TODO: Ci sono notifiche*/ true) {
    let notificationsNumber = 1;
    $('#profile-nav-element').append(' <span class="badge bg-info">' + notificationsNumber + '</span><span class="visually-hidden">Nuove notifiche</span>');
}
