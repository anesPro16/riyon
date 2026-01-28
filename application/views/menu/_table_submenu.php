<?php $i = 1; ?>
<?php foreach ($subMenu as $sm) : ?>
    <tr>
        <td><?= $i++; ?></td>
        <td><?= $sm['title']; ?></td>
        <td><?= $sm['menu_name']; ?></td>
        <td><?= $sm['url']; ?></td>
        <td><?= $sm['icon']; ?></td>
        <td>
            <?php if ($sm['is_active'] == 1) : ?>
                <span class="badge bg-success">Aktif</span>
            <?php else : ?>
                <span class="badge bg-danger">Nonaktif</span>
            <?php endif; ?>
        </td>
        <td>
            <button class="btn btn-warning btn-sm btn-edit" 
                data-id="<?= $sm['id']; ?>" 
                data-title="<?= htmlspecialchars($sm['title']); ?>" 
                data-menu-id="<?= $sm['menu_id']; ?>" 
                data-url="<?= htmlspecialchars($sm['url']); ?>" 
                data-icon="<?= htmlspecialchars($sm['icon']); ?>" 
                data-is-active="<?= $sm['is_active']; ?>">
                <i class="fas fa-edit"></i> Edit
            </button>
            
            <button class="btn btn-danger btn-sm btn-delete" 
                data-id="<?= $sm['id']; ?>" 
                data-title="<?= htmlspecialchars($sm['title']); ?>">
                <i class="fas fa-trash"></i> Hapus
            </button>
        </td>
    </tr>
<?php endforeach; ?>