import CrudHandler from './crud_handler.js'; // (Pastikan path ini benar)

document.addEventListener('DOMContentLoaded', () => {

  const csrfEl = document.querySelector('input[name="' + window.CSRF_TOKEN_NAME + '"]');
  const IS_ADMIN_OR_GURU = window.IS_ADMIN_OR_GURU || false;
  const CURRENT_TOPIC_ID = window.CURRENT_TOPIC_ID;
  const CURRENT_USER_ID = window.CURRENT_USER_ID;

  if (!CURRENT_TOPIC_ID) {
    console.error('TOPIC ID tidak ditemukan.');
    return;
  }

  const csrfConfig = {
    tokenName: window.CSRF_TOKEN_NAME,
    tokenHash: csrfEl ? csrfEl.value : ''
  };

  // --- Konfigurasi CRUD untuk Postingan Forum ---
  const config = {
    baseUrl: window.BASE_URL,
    entityName: 'Postingan',
    modalId: 'postModal',
    formId: 'postForm',
    modalLabelId: 'postModalLabel',
    hiddenIdField: 'postId',
    tableId: 'postsTable',
    btnAddId: 'btnAddPost',
    tableParentSelector: '#postsTableContainer', // Targetkan ID spesifik
    csrf: csrfConfig,
    urls: {
      load: IS_ADMIN_OR_GURU ? `guru/pbl_forum/get_posts/${CURRENT_TOPIC_ID}` : `siswa/pbl_forum/get_posts/${CURRENT_TOPIC_ID}`,
      save: IS_ADMIN_OR_GURU ? `guru/pbl_forum/save_post` : `siswa/pbl_forum/save_post`,
      delete: (id) => IS_ADMIN_OR_GURU ? `guru/pbl_forum/delete_post/${id}` : `siswa/pbl_forum/delete_post/${id}`,
    },
    deleteMethod: 'POST',
    modalTitles: { add: 'Tulis Balasan', edit: 'Edit Postingan' },
    deleteNameField: 'content',

    /**
     * dataMapper sekarang mengembalikan array dari 2 elemen
     * untuk 2 kolom (Postingan dan Aksi).
     */
    dataMapper: (item, i) => {

      // (Logika ini sudah benar)
      const itemUserId = (item.user_id || '').toString().trim();
      const currentUserId = (CURRENT_USER_ID || '').toString().trim();
      const isOwner = (itemUserId === currentUserId && itemUserId !== '');
      // const displayName = isOwner ? 'Anda (Guru)' : item.name;
      const displayName = item.name;

      // (Logika ini sudah benar)
      let buttons = '';
      if (isOwner) {
        buttons = `
          <button class="btn btn-sm btn-link text-warning btn-edit"
            data-id="${item.id}"
            data-post_content="${item.post_content}">
            Edit
          </button>
          <button class="btn btn-sm btn-link text-danger btn-delete"
            data-id="${item.id}"
            data-content="Postingan ini">
            Hapus
          </button>
        `;
      }

      // (Logika ini sudah benar)
      const postDate = new Date(item.created_at).toLocaleString('id-ID', {
        dateStyle: 'medium',
        timeStyle: 'short'
      });

      // [BARU] Ini adalah HTML untuk Kolom 1 (Postingan)
      // Tidak ada <hr> dan tidak ada <tr>/<td>
      const postContentHtml = `
        <div class="d-flex post-content-cell">
          <div class="flex-shrink-0">
            <i class="bi bi-person-circle fs-3 text-muted"></i>
          </div>
          <div class="flex-grow-1 ms-3">
            <div>
              <strong class="mb-0">${displayName}</strong>
              <small class="text-muted d-block">${postDate}</small>
            </div>
            <p class="mt-2 mb-0">${item.post_content.replace(/\n/g, '<br>')}</p>
          </div>
        </div>
      `;

      // Ini adalah HTML untuk Kolom 2 (Aksi)
      const postActionsHtml = `
        <div class="post-actions-cell">
          ${buttons}
        </div>
      `;
      
      // Kembalikan sebagai array [Kolom1, Kolom2]
      return [
        postContentHtml,
        postActionsHtml
      ];
    },
    
    formPopulator: (form, data) => {
      form.querySelector('#postId').value = data.id;
      form.querySelector('#post_content').value = data.post_content;
    },
    
    onAdd: (form) => {
        form.reset();
        form.querySelector('#postId').value = '';
    }
  };

  // Inisialisasi handler
  const postHandler = new CrudHandler(config);
  postHandler.init();
  
});