<footer class="site-footer">
    <div class="footer-inner">
        <a href="/artisphere/?controller=apropos&action=index" class="footer-link">À propos de nous</a>
        <a href="/artisphere/?controller=mentions&action=index" class="footer-link">Mentions légales</a>
        <a href="/artisphere/?controller=FAQ&action=index" class="footer-link">FAQ</a>
    </div>

    <!--rajoute un fichier js si il y en a un de spécifier dans le controleur-->
    <?php if (!empty($pageJs)): ?>
        <?php foreach ((array)$pageJs as $js): ?>
            <script src="/artisphere/js/<?= htmlspecialchars($js) ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</footer>
</body>
</html>