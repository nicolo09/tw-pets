<div class="container">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10 col-xl-8 mx-auto">
            <h2 class="h3 mb-4 page-title text-center">Impostazioni</h2>
            <h3 class="mb-0 mt-5">Impostazioni account</h3>
            <p>Impostazioni legate all'accesso e alla gestione del tuo account.</p>
            <div class="setting-section">
                <h4 class="mb-0">Informazioni di accesso</h4>
                <p>Cambia le informazioni di accesso legate al tuo account</p>
                <div class="list-group mb-5 shadow">
                    <div class="list-group-item">
                        <div class="row align-items-center">
                            <div class="col">
                                <label class="mb-0 fw-bolder" for="change-email">Email</label>
                                <p class="text-muted mb-0">Cambia l'email a cui invieremo notifiche e avvisi.</p>
                            </div>
                            <div class="col-auto">
                                <button class="btn btn-danger settings-item" id="change-email">Cambia</button>
                            </div>
                        </div>
                    </div>
                    <div class="list-group-item">
                        <div class="row align-items-center">
                            <div class="col">
                                <label class="mb-0 fw-bolder" for="change-password">Password</label>
                                <p class="text-muted mb-0">Cambia la password del tuo account.</p>
                            </div>
                            <div class="col-auto">
                                <button class="btn btn-danger settings-item" id="change-password">Cambia</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <h3 class="mb-0 mt-5">Impostazioni notifiche</h3>
            <p>Seleziona le notifiche che vuoi ricevere.</p>
            <div class="setting-section">
                <h4 class="mb-0">Sicurezza</h4>
                <p>Avvisi di sicurezza riguardanti il tuo account.</p>
                <div class="list-group mb-5 shadow">
                    <div class="list-group-item">
                        <div class="row align-items-center">
                            <div class="col">
                                <label class="mb-0 fw-bolder" for="alert-new-access">Nuovi accessi</label>
                                <p class="text-muted mb-0">Notifiche su nuovi accessi effettuati al tuo account.</p>
                            </div>
                            <div class="col-auto">
                                <input type="checkbox" class="custom-control-input settings-item" id="alert-new-access" checked="false" disabled />
                            </div>
                        </div>
                    </div>
                    <div class="list-group-item">
                        <div class="row align-items-center">
                            <div class="col">
                                <label class="mb-0 fw-bolder" for="alert-change-password">Cambio password/email</label>
                                <p class="text-muted mb-0">Notifiche quando viene cambiata la password o l'email associate all'account.</p>
                            </div>
                            <div class="col-auto">
                                <input type="checkbox" class="custom-control-input settings-item" id="alert-change-password" checked="false" disabled />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="setting-section">
                <h4 class="mb-0">Profilo</h4>
                <p>Seleziona le notifiche riguardanti il tuo profilo che vuoi ricevere.</p>
                <div class="list-group mb-5 shadow">
                    <div class="list-group-item">
                        <div class="row align-items-center">
                            <div class="col">
                                <label class="mb-0 fw-bolder" for="alert-likes">Mi piace</label>
                                <p class="text-muted mb-0">Notificami quando qualcuno mette mi piace a un mio post.</p>
                            </div>
                            <div class="col-auto">
                                <input type="checkbox" class="custom-control-input settings-item" id="alert-likes" checked="false" disabled />
                            </div>
                        </div>
                    </div>
                    <div class="list-group-item">
                        <div class="row align-items-center">
                            <div class="col">
                                <label class="mb-0 fw-bolder" for="alert-comments">Commenti</label>
                                <p class="text-muted mb-0">Notificami quando qualcuno commenta un mio post.</p>
                            </div>
                            <div class="col-auto">
                                <input type="checkbox" class="custom-control-input settings-item" id="alert-comments" checked="false" disabled />
                            </div>
                        </div>
                    </div>
                    <div class="list-group-item">
                        <div class="row align-items-center">
                            <div class="col">
                                <label class="mb-0 fw-bolder" for="alert-follow">Followers</label>
                                <p class="text-muted mb-0">Notificami quando qualcuno inizia a seguirmi.</p>
                            </div>
                            <div class="col-auto">
                                <input type="checkbox" class="custom-control-input settings-item" id="alert-follow" checked="false" disabled />
                            </div>
                        </div>
                    </div>
                    <div class="list-group-item">
                        <div class="row align-items-center">
                            <div class="col">
                                <label class="mb-0 fw-bolder" for="alert-follow-animal">Followers animali</label>
                                <p class="text-muted mb-0">Notificami quando qualcuno inizia a seguire un mio animale.</p>
                            </div>
                            <div class="col-auto">
                                <input type="checkbox" class="custom-control-input settings-item" id="alert-follow-animal" checked="false" disabled />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="setting-section">
                <h4 class="mb-0">Social</h4>
                <p>Seleziona le notifiche riguardanti le funzioni social che vuoi ricevere.</p>
                <div class="list-group mb-5 shadow">
                    <div class="list-group-item">
                        <div class="row align-items-center">
                            <div class="col">
                                <label class="mb-0 fw-bolder" for="alert-new-post-person">Post persone che seguo</label>
                                <p class="text-muted mb-0">Notificami quando una persona che seguo pubblica un nuovo post.</p>
                            </div>
                            <div class="col-auto">
                                <input type="checkbox" class="custom-control-input settings-item" id="alert-new-post-person" checked="false" disabled />
                            </div>
                        </div>
                    </div>
                    <div class="list-group-item">
                        <div class="row align-items-center">
                            <div class="col">
                                <label class="mb-0 fw-bolder" for="alert-new-post-animal">Post animali che seguo</label>
                                <p class="text-muted mb-0">Notificami quando un animale che seguo pubblica un nuovo post.</p>
                            </div>
                            <div class="col-auto">
                                <input type="checkbox" class="custom-control-input settings-item" id="alert-new-post-animal" checked="false" disabled />
                            </div>
                        </div>
                    </div>
                    <div class="list-group-item">
                        <div class="row align-items-center">
                            <div class="col">
                                <label class="mb-0 fw-bolder" for="alert-comment-reply">Risposte ai miei commenti</label>
                                <p class="text-muted mb-0">Notificami quando qualcuno risponde a un mio commento.</p>
                            </div>
                            <div class="col-auto">
                                <input type="checkbox" class="custom-control-input settings-item" id="alert-comment-reply" checked="false" disabled />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src=/js/settings.js></script>