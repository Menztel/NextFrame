<section class="menu-list-container">
    <h1>Gestion des menus</h1>
    
    <!-- Formulaire pour créer un nouvel élément de menu -->
    <form method="POST" action="/dashboard/menu/save">
        <input type="hidden" name="id" value="<?= $menu['id'] ?? '' ?>">
        <label for="label">Nom du menu :</label>
        <input type="text" name="label" value="<?= $menu['label'] ?? '' ?>" required>
        
        <label for="url">URL :</label>
        <input type="text" name="url" value="<?= $menu['url'] ?? '' ?>" required>
        
        <label for="position">Position :</label>
        <input type="number" name="position" value="<?= $menu['position'] ?? '' ?>" required>
        
        <label for="parent_id">Menu parent :</label>
        <select name="parent_id">
            <option value="">Aucun</option>
            <?php foreach ($data as $parentMenu): ?>
                <option value="<?= $parentMenu['id'] ?>" <?= isset($menu['parent_id']) && $menu['parent_id'] == $parentMenu['id'] ? 'selected' : '' ?>>
                    <?= $parentMenu['label'] ?>
                </option>
            <?php endforeach; ?>
        </select>
        
        <button type="submit">Enregistrer</button>
    </form>

    <!-- Liste des éléments de menu -->
    <table>
        <thead>
            <tr>
                <th>Nom</th>
                <th>URL</th>
                <th>Position</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data as $menuItem): ?>
                <tr>
                    <td><?= $menuItem['label'] ?></td>
                    <td><?= $menuItem['url'] ?></td>
                    <td><?= $menuItem['position'] ?></td>
                    <td>
                        <form method="POST" action="/dashboard/menu/save">
                            <input type="hidden" name="id" value="<?= $menuItem['id'] ?>">
                            <button type="submit">Modifier</button>
                        </form>
                        <form method="POST" action="/dashboard/menu/delete">
                            <input type="hidden" name="id" value="<?= $menuItem['id'] ?>">
                            <button type="submit">Supprimer</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>
