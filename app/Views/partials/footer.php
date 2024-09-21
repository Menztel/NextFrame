<footer class="footer">
    <div class="content">
        <div class="column logo-column">
            <div class="content-info">
                <div class="address-block">
                    <div class="address">Address:</div>
                    <div class="address-detail">
                        <?= $adresse ?? '18 Rue des étudiants, 12345 France' ?>
                    </div>
                </div>
                <div class="contact-block">
                    <div class="contact">Contact:</div>
                    <div class="contact-info">
                        <div class="phone">
                            <?= $phone ?? '01 02 03 04 05' ?>
                        </div>
                        <div class="email">
                            <?= $email ?? 'contact@nextframe.fr' ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="credits">
        <div class="divider"></div>
        <div class="row">
            <div class="copyright">© 2024 Nextframe. Tous droits réservés.</div>
            <div class="footer-links">
                <div class="footer-link">
                    <a href="#">Politique de confidentialité</a>
                </div>
                <div class="footer-link">
                    <a href="#">Conditions d'utilisation</a>
                </div>
            </div>
        </div>
    </div>
    </>