<!-- ======= Sidebar ======= -->
<aside id="sidebar" class="sidebar">
  <ul class="sidebar-nav" id="sidebar-nav">

    <?php $menus = get_menu_sidebar(); ?>

    <?php foreach ($menus as $m) : ?>
      <li class="nav-heading"><?= $m['menu']; ?></li>

      <?php foreach ($m['submenu'] as $sm) : ?>
        <li class="nav-item">
          <a class="nav-link <?= ($title == $sm['title']) ? '' : 'collapsed'; ?>" href="<?= base_url($sm['url']); ?>">
            <i class="<?= $sm['icon']; ?>"></i>
            <span><?= $sm['title']; ?></span>
          </a>
        </li>
      <?php endforeach; ?>

    <?php endforeach; ?>

    <!-- <li class="nav-heading">Akun</li>
    <li class="nav-item">
      <a class="nav-link collapsed" href="</?= base_url('auth/logout'); ?>">
        <i class="bi bi-box-arrow-right"></i>
        <span>Logout</span>
      </a>
    </li> -->

  </ul>
</aside>
<!-- End Sidebar -->
