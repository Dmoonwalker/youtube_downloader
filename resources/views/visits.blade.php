<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visits</title>
    <!-- Include your CSS here -->
    <style>
        .table-container {
            padding: 20px;
            background-color: #f8f9fa;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        .table {
            margin-bottom: 0;
        }

        .thead-dark th {
            background-color: #264653;
            color: #ffffff;
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(38, 70, 83, 0.1);
        }

        .table th, .table td {
            text-align: center;
            vertical-align: middle;
        }

        .table th {
            font-weight: bold;
        }

        .table-bordered th, .table-bordered td {
            border: 1px solid #dee2e6;
        }

        h2 {
            color: #264653;
        }

        @media (max-width: 768px) {
            .table-responsive {
                margin-bottom: 15px;
            }

            .table thead {
                display: none;
            }

            .table tr {
                display: block;
                margin-bottom: 10px;
            }

            .table td {
                display: block;
                text-align: right;
                padding-left: 50%;
                position: relative;
            }

            .table td::before {
                content: attr(data-label);
                position: absolute;
                left: 10px;
                width: 50%;
                padding-right: 10px;
                text-align: left;
                font-weight: bold;
            }
        }
    </style>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div id="visitsContainer" class="row" style="display: none;">
            <div class="col-md-12">
                <h2>Visits</h2>
                <!-- Total Visits Display -->
                <div class="mb-3">
                    <strong>Total Visits:</strong> <span id="totalVisits">0</span>
                </div>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>IP Address</th>
                            <th>User Agent</th>
                            <th>Visited At</th>
                        </tr>
                    </thead>
                    <tbody id="visitsTableBody">
                        <!-- Visits will be loaded here dynamically -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Password Modal -->
    <div class="modal fade" id="passwordModal" tabindex="-1" role="dialog" aria-labelledby="passwordModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="passwordModalLabel">Enter Password</h5>
                </div>
                <div class="modal-body">
                    <div id="passwordError" class="alert alert-danger" style="display: none;">Incorrect password. Please try again.</div>
                    <div id="serverError" class="alert alert-danger" style="display: none;">Server error. Please try again later.</div>
                    <form id="passwordForm">
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" id="password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            // Show the password modal on page load
            $('#passwordModal').modal('show');

            $('#passwordForm').on('submit', function(event) {
                event.preventDefault();
                let password = $('#password').val();

                $.ajax({
                    url: '{{ route('checkPasswordAndGetVisits') }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        password: password
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#passwordModal').modal('hide');
                            loadVisits(response.visits, response.count);
                        } else {
                            $('#passwordError').show();
                        }
                    },
                    error: function() {
                        $('#serverError').show();
                    }
                });
            });

            function loadVisits(visits, count) {
                // Display the total number of visits
                $('#totalVisits').text(count);

                let visitsTableBody = $('#visitsTableBody');
                visitsTableBody.empty();

                // Iterate through each visit and append to the table
                visits.forEach(function(visit) {
                    let row = `
                        <tr>
                            <td>${visit.ip_address}</td>
                            <td>${visit.user_agent}</td>
                            <td>${visit.visited_at}</td>
                        </tr>`;
                    visitsTableBody.append(row);
                });

                // Show the visits container
                $('#visitsContainer').show();
            }
        });
    </script>
</body>
</html>
