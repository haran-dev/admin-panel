  <!-- ======= Footer ======= -->
  <footer id="footer" class="footer">
    <div class="copyright">
      &copy; Copyright <strong><span>BTEC Solution</span></strong>. All Rights Reserved
    </div>
    <div class="credits">
      Designed by <a href="https://bootstrapmade.com/">BTEC Solution</a>
    </div>
  </footer><!-- End Footer -->

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>



  <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>



  <!-- Vendor JS Files -->
  <script src="{{ asset('assets/vendor/apexcharts/apexcharts.min.js') }}"></script>
  <script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('assets/vendor/chart.js/chart.umd.js') }}"></script>
  <script src="{{ asset('assets/vendor/echarts/echarts.min.js') }}"></script>
  <script src="{{ asset('assets/vendor/quill/quill.js') }}"></script>
  {{-- <script src="{{ asset('assets/vendor/simple-datatables/simple-datatables.js') }}"></script> --}}

  <script src="{{ asset('assets/vendor/tinymce/tinymce.min.js') }}"></script>
  <script src="{{ asset('assets/vendor/php-email-form/validate.js') }}"></script>


  <script src="https://cdn.datatables.net/1.11.4/js/jquery.dataTables.min.js"></script>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.js"></script>


  <script>
    function editLink(url, actionId, title, icon, color) {
      if (!title) title = 'Edit Item';
      if (!icon) icon = 'fa-solid fa-pen';
      if (!color) color = 'text-secondary';

      return "<a data-postdata='{\"id\":\"" + actionId +
        "\", \"actionType\":\"edit\", \"_token\": \"{{ csrf_token() }}\"}' " +
        "class='details-edit-button table-action-buttons me-4' href='#' title='" +
        title + "' data-url='" + url + "'>" +
        "<i class='" + icon + " " + color + "'></i></a>";
    }

    function deleteLink(url, actionId, color = 'text-danger') {
      return "<a data-postdata='{\"id\":\"" + actionId +
        "\", \"actionType\":\"delete\", \"_token\": \"{{ csrf_token() }}\"}' " +
        "class='row-delete-button' href='#' data-url='" +
        url + "'><i class='fa-solid fa-trash " + color + "'></i></a>";
    }
  </script>



  <script src="{{ asset('assets/notify/js/notify.js') }}"></script>
  <script src="{{ asset('assets/notify/js/notify.min.js') }}"></script>

  <script src="{{ asset('assets/js/common.js') }}"></script>
  <script src="{{ asset('assets/js/main.js') }}"></script>


  <script>
    $('#accessDeniedModal').on('hidden.bs.modal', function() {
      location.reload();
    });
  </script>


  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const input = document.getElementById('menuSearchInput');
      const resultsBox = document.getElementById('menuSearchResults');

      input.addEventListener('keyup', function() {
        const query = input.value.trim();

        if (query.length < 1) {
          resultsBox.style.display = 'none';
          resultsBox.innerHTML = '';
          return;
        }

        fetch(`/menu-search?query=${query}`)
          .then(response => response.json())
          .then(data => {
            if (data.length === 0) {
              resultsBox.style.display = 'none';
              resultsBox.innerHTML = '';
              return;
            }

            resultsBox.innerHTML = '';
            data.forEach(menu => {
              const item = document.createElement('li');
              item.classList.add('list-group-item', 'list-group-item-action');
              item.textContent = menu.title;
              item.style.cursor = 'pointer';
              item.addEventListener('click', () => {
                window.location.href = menu.link;
              });
              resultsBox.appendChild(item);
            });

            resultsBox.style.display = 'block';
          });
      });

      // Hide when clicking outside
      document.addEventListener('click', function(event) {
        if (!input.contains(event.target) && !resultsBox.contains(event.target)) {
          resultsBox.style.display = 'none';
        }
      });
    });
  </script>





















  </body>

  </html>