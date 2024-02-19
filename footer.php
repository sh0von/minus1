
  
  <script>
    document.addEventListener('DOMContentLoaded', () => {

     
      const $navbarBurgers = Array.prototype.slice.call(document.querySelectorAll('.navbar-burger'), 0);

     
      if ($navbarBurgers.length > 0) {

       
        $navbarBurgers.forEach( el => {
          el.addEventListener('click', () => {

           
            const target = el.dataset.target;
            const $target = document.getElementById(target);

           
            el.classList.toggle('is-active');
            $target.classList.toggle('is-active');

          });
        });
      }

    });
  </script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const notificationIcon = document.getElementById('notificationIcon');
      const notificationModal = document.getElementById('notificationModal');
      const modalClose = document.querySelector('.modal-close');

      notificationIcon.addEventListener('click', function () {
        notificationModal.classList.add('is-active');
      });

      modalClose.addEventListener('click', function () {
        notificationModal.classList.remove('is-active');
      });
    });
  </script><script>
      feather.replace();
    </script>
</body>
</html>
