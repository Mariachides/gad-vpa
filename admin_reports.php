<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
include 'config.php';

// ================== SUMMARY / FILTER LOGIC ==================

// Get selected filter (default: current month/year)
$selectedMonth = isset($_GET['month']) ? (int)$_GET['month'] : (int)date('n');
$selectedYear  = isset($_GET['year'])  ? (int)$_GET['year']  : (int)date('Y');

// Clamp month to a valid range just in case
if ($selectedMonth < 1 || $selectedMonth > 12) {
    $selectedMonth = (int)date('n');
}

$monthNames = [
    1=>'January', 2=>'February', 3=>'March', 4=>'April', 5=>'May', 6=>'June',
    7=>'July', 8=>'August', 9=>'September', 10=>'October', 11=>'November', 12=>'December'
];

// Count of reports for the selected month & year (prepared statement)
$summaryStmt = mysqli_prepare($conn, "
    SELECT COUNT(*) as total 
    FROM `incident_reports` 
    WHERE MONTH(created_at) = ? AND YEAR(created_at) = ?
");
mysqli_stmt_bind_param($summaryStmt, "ii", $selectedMonth, $selectedYear);
mysqli_stmt_execute($summaryStmt);
$summaryTotal = mysqli_fetch_assoc(mysqli_stmt_get_result($summaryStmt))['total'];
mysqli_stmt_close($summaryStmt);

// Get all years that have reports, for the year dropdown
$yearsQuery = mysqli_query($conn, "SELECT DISTINCT YEAR(created_at) as yr FROM `incident_reports` ORDER BY yr DESC");
$availableYears = [];
while ($y = mysqli_fetch_assoc($yearsQuery)) {
    $availableYears[] = (int)$y['yr'];
}
if (empty($availableYears)) {
    $availableYears[] = (int)date('Y');
}

// Always offer a range of years even if some have no reports yet,
// so future years (e.g. 2027) can still be selected and filtered.
$earliestYear = min($availableYears);
$futureYearsAhead = 2; // how many years beyond today to always show; adjust as needed
$latestYear = max((int)date('Y') + $futureYearsAhead, max($availableYears));

for ($yr = $earliestYear; $yr <= $latestYear; $yr++) {
    if (!in_array($yr, $availableYears)) {
        $availableYears[] = $yr;
    }
}

// Make sure the currently selected year is always an option (e.g. a very old/odd year passed via URL)
if (!in_array($selectedYear, $availableYears)) {
    $availableYears[] = $selectedYear;
}

rsort($availableYears);

// Full month-by-month breakdown for the selected year (used for the mini bar chart)
$breakdownStmt = mysqli_prepare($conn, "
    SELECT MONTH(created_at) as mo, COUNT(*) as total
    FROM `incident_reports`
    WHERE YEAR(created_at) = ?
    GROUP BY MONTH(created_at)
");
mysqli_stmt_bind_param($breakdownStmt, "i", $selectedYear);
mysqli_stmt_execute($breakdownStmt);
$breakdownResult = mysqli_stmt_get_result($breakdownStmt);

$monthlyBreakdown = array_fill(1, 12, 0);
while ($m = mysqli_fetch_assoc($breakdownResult)) {
    $monthlyBreakdown[(int)$m['mo']] = (int)$m['total'];
}
mysqli_stmt_close($breakdownStmt);
$maxMonthlyCount = max($monthlyBreakdown) ?: 1; // avoid divide-by-zero in bar heights

// Whether an explicit filter was applied (so table filtering below only kicks in when requested)
$filterApplied = isset($_GET['month']) || isset($_GET['year']);

// ================== FETCH REPORTS FOR TABLE ==================
if ($filterApplied) {
    $listStmt = mysqli_prepare($conn, "
        SELECT * FROM `incident_reports`
        WHERE MONTH(created_at) = ? AND YEAR(created_at) = ?
        ORDER BY created_at DESC
    ");
    mysqli_stmt_bind_param($listStmt, "ii", $selectedMonth, $selectedYear);
    mysqli_stmt_execute($listStmt);
    $result = mysqli_stmt_get_result($listStmt);
} else {
    $result = mysqli_query($conn, "SELECT * FROM `incident_reports` ORDER BY created_at DESC");
}

// ================== DATA FOR PRINTABLE SUMMARY ==================
// Always reflects the selected month/year (regardless of whether the table above is filtered),
// so "Print Summary" prints the report for whatever period is currently selected.
$printStmt = mysqli_prepare($conn, "
    SELECT * FROM `incident_reports`
    WHERE MONTH(created_at) = ? AND YEAR(created_at) = ?
    ORDER BY created_at ASC
");
mysqli_stmt_bind_param($printStmt, "ii", $selectedMonth, $selectedYear);
mysqli_stmt_execute($printStmt);
$printResult = mysqli_stmt_get_result($printStmt);
$printRows = [];
while ($pr = mysqli_fetch_assoc($printResult)) {
    $printRows[] = $pr;
}
mysqli_stmt_close($printStmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Academic Reports | Admin Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #1e3a8a; 
            --sidebar-color: #ffffff;
            --bg-color: #f3f4f6;
            --text-main: #1f2937;
        }

        body { 
            display: flex; 
            min-height: 100vh; 
            background-color: var(--bg-color);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--text-main);
        }

        .sidebar { 
            width: 280px; 
            background-color: var(--sidebar-color); 
            border-right: 1px solid #e5e7eb;
            flex-shrink: 0; 
            transition: all 0.3s;
            position: sticky;
            top: 0;
            height: 100vh;
        }

        .sidebar-header {
            padding: 2rem 1.5rem;
            border-bottom: 1px solid #f3f4f6;
        }

        .nav-link {
            padding: 0.8rem 1.5rem;
            color: #4b5563;
            font-weight: 500;
            display: flex;
            align-items: center;
            border-radius: 8px;
            margin: 0.2rem 1rem;
            transition: all 0.2s;
            text-decoration: none;
        }

        .nav-link i {
            font-size: 1.2rem;
            margin-right: 12px;
        }

        .nav-link:hover {
            background-color: #f3f4f6;
            color: var(--primary-color);
        }

        .nav-link.active {
            background-color: #eff6ff;
            color: var(--primary-color);
        }

        .main-content { 
            flex-grow: 1; 
            padding: 2.5rem; 
        }

        .card-custom { 
            border: none; 
            border-radius: 12px; 
            background: white; 
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); 
        }

        .table thead { 
            background-color: #f8fafc; 
            color: #475569; 
            font-size: 0.85rem; 
            text-transform: uppercase; 
        }

        .logout-btn {
            margin-top: auto;
            border-top: 1px solid #f3f4f6;
            padding: 1.5rem;
        }
        
        /* Blur effect placeholder para sa description */
        .hidden-desc {
            font-style: italic;
            color: #9ca3af;
            font-size: 0.9rem;
        }

        /* ===== Summary section styles ===== */
        .summary-count {
            font-size: 2.75rem;
            line-height: 1;
        }

        .mini-chart {
            display: flex;
            align-items: flex-end;
            gap: 6px;
            height: 90px;
            margin-top: 0.5rem;
        }

        .mini-chart .bar-col {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-end;
            height: 100%;
        }

        .mini-chart .bar {
            width: 100%;
            max-width: 22px;
            background-color: #dbeafe;
            border-radius: 4px 4px 0 0;
            transition: background-color 0.2s;
        }

        .mini-chart .bar.active {
            background-color: var(--primary-color);
        }

        .mini-chart .bar-label {
            font-size: 0.65rem;
            color: #9ca3af;
            margin-top: 4px;
        }

        /* ===== Printable summary (hidden on screen, shown only when printing) ===== */
        #printSummary {
            display: none;
        }

        @media print {
            body * {
                visibility: hidden;
            }
            #printSummary, #printSummary * {
                visibility: visible;
            }
            #printSummary {
                display: block;
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                padding: 20px;
            }
            #printSummary .print-header {
                text-align: center;
                border-bottom: 2px solid #1e3a8a;
                padding-bottom: 12px;
                margin-bottom: 20px;
            }
            #printSummary .print-header h2 {
                margin: 0;
                color: #1e3a8a;
            }
            #printSummary .print-meta {
                display: flex;
                justify-content: space-between;
                margin-bottom: 16px;
                font-size: 0.9rem;
            }
            #printSummary table {
                width: 100%;
                border-collapse: collapse;
                font-size: 0.85rem;
            }
            #printSummary th, #printSummary td {
                border: 1px solid #333;
                padding: 6px 8px;
                text-align: left;
                vertical-align: top;
            }
            #printSummary th {
                background-color: #f0f0f0;
            }
            #printSummary td.desc-cell {
                white-space: pre-wrap;
            }
            #printSummary .print-total {
                margin-top: 16px;
                font-weight: bold;
                text-align: right;
            }
        }
    </style>
</head>
<body>

<?php include 'includes/admin_nav.php'; ?>

<div class="main-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-1">Incident Reports</h2>
                <p class="text-muted">Reviewing and managing submitted faculty reports.</p>
            </div>
            <button class="btn btn-outline-primary shadow-sm" onclick="window.print()">
                <i class="bi bi-printer me-2"></i> Print Summary
            </button>
        </div>

        <!-- ================== PRINTABLE SUMMARY (hidden on screen) ================== -->
        <div id="printSummary">
            <div class="print-header">
                <h2>Incident Report Summary</h2>
                <div><?php echo $monthNames[$selectedMonth] . ' ' . $selectedYear; ?></div>
            </div>
            <div class="print-meta">
                <span>Created on: <?php echo date('M d, Y h:i A'); ?></span>
                <span>Prepared by: <?php echo htmlspecialchars($_SESSION['username']); ?></span>
            </div>

            <table>
                <thead>
                    <tr>
                        <th style="width: 4%;">#</th>
                        <th style="width: 16%;">Reported By</th>
                        <th style="width: 13%;">Incident Date</th>
                        <th style="width: 17%;">Date Submitted</th>
                        <th style="width: 50%;">Description</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($printRows) > 0): ?>
                        <?php foreach ($printRows as $i => $pr): ?>
                        <tr>
                            <td><?php echo $i + 1; ?></td>
                            <td><?php echo htmlspecialchars($pr['reported_by'] ?? 'Anonymous'); ?></td>
                            <td><?php echo date('M d, Y', strtotime($pr['incident_date'])); ?></td>
                            <td><?php echo date('M d, Y, h:i A', strtotime($pr['created_at'])); ?></td>
                            <td class="desc-cell"><?php echo nl2br(htmlspecialchars($pr['description'])); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align:center;">No incident reports for this period.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <div class="print-total">
                Total Incident Reports: <?php echo count($printRows); ?>
            </div>
        </div>
        <!-- ================== END PRINTABLE SUMMARY ================== -->

        <!-- ================== SUMMARY + FILTER ================== -->
        <div class="row mb-4 g-3">
            <div class="col-md-3">
                <div class="card card-custom p-4 text-center h-100 d-flex flex-column justify-content-center">
                    <small class="text-uppercase text-muted fw-bold mb-2">
                        <?php echo $monthNames[$selectedMonth]; ?> <?php echo $selectedYear; ?>
                    </small>
                    <div class="fw-bold text-primary summary-count"><?php echo $summaryTotal; ?></div>
                    <span class="text-muted small">Incident Report<?php echo $summaryTotal == 1 ? '' : 's'; ?></span>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card card-custom p-4 h-100">
                    <form method="GET" class="d-flex gap-2 align-items-end flex-wrap">
                        <div class="flex-grow-1">
                            <label class="form-label fw-bold small mb-1">Month</label>
                            <select name="month" class="form-select">
                                <?php foreach ($monthNames as $num => $name): ?>
                                    <option value="<?php echo $num; ?>" <?php echo $num == $selectedMonth ? 'selected' : ''; ?>>
                                        <?php echo $name; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="flex-grow-1">
                            <label class="form-label fw-bold small mb-1">Year</label>
                            <select name="year" class="form-select">
                                <?php foreach ($availableYears as $yr): ?>
                                    <option value="<?php echo $yr; ?>" <?php echo $yr == $selectedYear ? 'selected' : ''; ?>>
                                        <?php echo $yr; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-filter me-1"></i> Filter
                        </button>
                        <?php if ($filterApplied): ?>
                            <a href="<?php echo htmlspecialchars(strtok($_SERVER['REQUEST_URI'], '?')); ?>" class="btn btn-outline-secondary" title="Clear filter">
                                <i class="bi bi-x-lg"></i>
                            </a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>

            <div class="col-md-5">
                <div class="card card-custom p-3 h-100">
                    <small class="text-uppercase text-muted fw-bold mb-1">
                        Reports per Month &mdash; <?php echo $selectedYear; ?>
                    </small>
                    <div class="mini-chart">
                        <?php foreach ($monthNames as $num => $name): ?>
                            <div class="bar-col" title="<?php echo $name . ': ' . $monthlyBreakdown[$num]; ?>">
                                <div class="bar <?php echo $num == $selectedMonth ? 'active' : ''; ?>"
                                     style="height: <?php echo max(4, round(($monthlyBreakdown[$num] / $maxMonthlyCount) * 100)); ?>%;">
                                </div>
                                <div class="bar-label"><?php echo substr($name, 0, 1); ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        <!-- ================== END SUMMARY + FILTER ================== -->

        <div class="mb-4">
            <div class="input-group shadow-sm" style="max-width: 500px;">
                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                <input type="text" id="reportSearch" class="form-control border-start-0" placeholder="Search by reported by or date...">
            </div>
        </div>

        <div class="card card-custom overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead>
                            <tr>
                                <th class="px-4 py-3">Reported By</th>
                                <th class="py-3">Description</th>
                                <th class="py-3 text-center">Date Submitted</th>
                                <th class="py-3 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="reportTableBody">
                            <?php if(mysqli_num_rows($result) > 0): ?>
                                <?php while($row = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td class="px-4">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-light rounded-circle p-2 me-2">
                                                <i class="bi bi-person-badge text-primary"></i>
                                            </div>
                                            <span class="fw-bold"><?php echo htmlspecialchars($row['reported_by']); ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="hidden-desc">
                                            <i class="bi bi-lock-fill me-1"></i> Content encrypted. Click view to read.
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark border">
                                            <i class="bi bi-calendar-event me-1"></i>
                                            <?php echo date('M d, Y', strtotime($row['created_at'])); ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <button class="btn btn-sm btn-outline-primary"
                                                    data-id="<?php echo $row['id']; ?>"
                                                    data-title="<?php echo htmlspecialchars($row['title']); ?>"
                                                    data-reported-by="<?php echo htmlspecialchars($row['reported_by'] ?? 'Anonymous'); ?>"
                                                    data-incident-date="<?php echo date('M d, Y', strtotime($row['incident_date'])); ?>"
                                                    data-created-at="<?php echo date('M d, Y, h:i A', strtotime($row['created_at'])); ?>"
                                                    onclick="viewProtectedReport(this)">
                                                View
                                            </button>
                                            <a href="delete_report.php?id=<?php echo $row['id']; ?>" 
                                               class="btn btn-sm btn-outline-danger" 
                                               onclick="return confirm('Sigurado ka bang gusto mong burahin ang report na ito?');">
                                                 <i class="bi bi-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center py-5 text-muted">
                                        <i class="bi bi-folder-x fs-2"></i><br>
                                        No incident reports found<?php echo $filterApplied ? ' for ' . $monthNames[$selectedMonth] . ' ' . $selectedYear : ''; ?>.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="viewReportModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px; overflow: hidden; border: none;">
            <div class="modal-header" style="background-color: var(--primary-color); color: white;">
                <h5 class="modal-title"><i class="bi bi-shield-lock me-2"></i> Protected Report Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <h5 class="fw-bold text-primary mb-4" id="metaTitle"><i class="bi bi-shield-lock me-2"></i></h5>
                <div class="row g-4">
                    <div class="col-md-5">
                        <div class="d-flex flex-column gap-3">
                            <div class="p-3 border rounded bg-light">
                                <small class="text-uppercase text-muted fw-bold d-block mb-1">Reported By</small>
                                <span class="fw-semibold h6 mb-0" id="metaReportedBy">—</span>
                            </div>
                            <div class="p-3 border rounded bg-light">
                                <small class="text-uppercase text-muted fw-bold d-block mb-1">Date &amp; Time Submitted</small>
                                <span class="fw-semibold h6 mb-0" id="metaCreatedAt">—</span>
                            </div>
                            <div class="p-3 border rounded bg-light">
                                <small class="text-uppercase text-muted fw-bold d-block mb-1">Incident Date</small>
                                <span class="fw-semibold h6 mb-0" id="metaIncidentDate">—</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-7">
                        <div id="report-password-section">
                            <label class="form-label fw-bold">Admin Password</label>
                            <div class="input-group">
                                <input type="password" id="reportPasswordInput" class="form-control" placeholder="Enter admin password..." autocomplete="off">
                                <button class="btn btn-primary" onclick="unlockReport()"><i class="bi bi-unlock me-1"></i> Unlock</button>
                            </div>
                            <div id="report-password-error" class="text-danger small mt-2" style="display: none;">
                                <i class="bi bi-exclamation-circle me-1"></i> Maling password. Hindi maaaring ipakita ang detalye.
                            </div>
                        </div>
                        <div id="report-description-section" style="display: none;">
                            <h6 class="fw-bold text-uppercase text-muted mb-2">Full Report Description</h6>
                            <div id="reportDescription" style="white-space: pre-wrap; background: #f8fafc; border: 1px solid #e5e7eb; border-radius: 8px; padding: 16px; min-height: 120px;"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
let currentReportId = null;

function viewProtectedReport(btn) {
    currentReportId = btn.getAttribute('data-id');
    document.getElementById('metaTitle').textContent = btn.getAttribute('data-title');
    document.getElementById('metaReportedBy').textContent = btn.getAttribute('data-reported-by');
    document.getElementById('metaCreatedAt').textContent = btn.getAttribute('data-created-at');
    document.getElementById('metaIncidentDate').textContent = btn.getAttribute('data-incident-date');
    document.getElementById('report-password-section').style.display = 'block';
    document.getElementById('report-description-section').style.display = 'none';
    document.getElementById('reportPasswordInput').value = '';
    document.getElementById('report-password-error').style.display = 'none';
    new bootstrap.Modal(document.getElementById('viewReportModal')).show();
}

function unlockReport() {
    const password = document.getElementById('reportPasswordInput').value;
    const errorEl = document.getElementById('report-password-error');

    fetch('verify_report_password.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `id=${encodeURIComponent(currentReportId)}&password=${encodeURIComponent(password)}`
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            document.getElementById('report-description-section').style.display = 'block';
            document.getElementById('report-password-section').style.display = 'none';
            document.getElementById('reportDescription').textContent = data.description;
        } else {
            errorEl.textContent = data.message || 'Maling password.';
            errorEl.style.display = 'block';
        }
    })
    .catch(() => {
        errorEl.textContent = 'May error, subukan ulit.';
        errorEl.style.display = 'block';
    });
}

document.getElementById('reportPasswordInput').addEventListener('keydown', function(e) {
    if (e.key === 'Enter') unlockReport();
});

document.getElementById('reportSearch').addEventListener('keyup', function() {
    let filter = this.value.toLowerCase();
    let rows = document.querySelectorAll('#reportTableBody tr');
    rows.forEach(row => {
        // In-adjust para i-search lang yung "Reported By" since nakatago na yung desc
        let reportedBy = row.querySelector('td:first-child').textContent.toLowerCase();
        let date = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
        row.style.display = (reportedBy.includes(filter) || date.includes(filter)) ? "" : "none";
    });
});
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>