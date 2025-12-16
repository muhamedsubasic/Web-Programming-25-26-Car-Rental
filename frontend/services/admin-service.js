/* AdminService: loads users and cars for admin and allows deleting users */
var AdminService = {
  categories: {},
  init: function() {
    const token = localStorage.getItem('user_token');
    if (!token) {
      toastr.error('You must be logged in as admin');
      window.location.replace('index.html');
      return;
    }
    const parsed = Utils.parseJwt(token);
    const role = parsed?.user?.role;
    if (role !== Constants.ADMIN_ROLE) {
      toastr.error('Access denied: admin only');
      window.location.replace('index.html');
      return;
    }
    AdminService.loadCategories()
      .then(() => {
        AdminService.loadUsers();
        AdminService.loadCars();
      })
      .catch(err => {
        console.error(err);
        toastr.error('Failed to load categories');
      });
  },

  loadCategories: function() {
    return new Promise(function(resolve, reject){
      RestClient.get('categories', function(res){
        if (Array.isArray(res)) {
          res.forEach(c => { AdminService.categories[c.id] = c.name; });
        }
        resolve();
      }, function(err){ reject(err); });
    });
  },

  loadUsers: function(){
    RestClient.get('users', function(res){
      const tbody = $('#users-table tbody');
      tbody.empty();
      if (!Array.isArray(res)) return;
      res.forEach(u => {
        const tr = $('<tr>');
        tr.append('<td>'+u.id+'</td>');
        tr.append('<td>'+((u.name||'') + ' ' + (u.surname||'')) +'</td>');
        tr.append('<td>'+ (u.email||'') +'</td>');
        tr.append('<td>'+ (u.phone||'') +'</td>');
        tr.append('<td>'+ (u.role||'') +'</td>');
        tr.append('<td>'+ (u.created_at||'') +'</td>');
        tr.append('<td><button class="btn btn-sm btn-danger btn-delete-user" data-id="'+u.id+'">Delete</button></td>');
        tbody.append(tr);
      });
      // bind delete
      $('.btn-delete-user').on('click', function(){
        const id = $(this).data('id');
        AdminService.deleteUser(id);
      });
    }, function(err){
      console.error(err);
      toastr.error('Failed to load users');
    });
  },

  loadCars: function(){
    RestClient.get('cars', function(res){
      const tbody = $('#cars-table tbody');
      tbody.empty();
      if (!Array.isArray(res)) return;
      res.forEach(c => {
        const catName = AdminService.categories[c.category_id] || c.category_id || '';
        const available = c.availability || c.available || c.is_available || c.availability === 1 ? 'Yes' : 'No';
        const tr = $('<tr>');
        tr.append('<td>'+c.id+'</td>');
        tr.append('<td>'+ (c.brand||'') +'</td>');
        tr.append('<td>'+ (c.model||'') +'</td>');
        tr.append('<td>'+ catName +'</td>');
        tr.append('<td>'+ (c.daily_rate||'') +'</td>');
        tr.append('<td>'+ available +'</td>');
        tbody.append(tr);
      });
    }, function(err){
      console.error(err);
      toastr.error('Failed to load cars');
    });
  },

  deleteUser: function(id){
    if (!confirm('Delete user #' + id + '? This cannot be undone.')) return;
    RestClient.delete('users/'+id, {}, function(res){
      toastr.success('User deleted');
      AdminService.loadUsers();
    }, function(err){
      console.error(err);
      toastr.error('Failed to delete user');
    });
  }
};
