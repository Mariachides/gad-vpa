<?php
session_start();

if(!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin'){
    header("Location: login.php");
    exit();
}

$file = 'gad_events.json';

// READ
function readData($file){
    if(!file_exists($file)){
        file_put_contents($file, json_encode([]));
    }
    return json_decode(file_get_contents($file), true);
}

// WRITE
function saveData($file, $data){
    file_put_contents($file, json_encode(array_values($data), JSON_PRETTY_PRINT));
}

$events = readData($file);

// CREATE / UPDATE
if(isset($_POST['save'])){
    $id = $_POST['id'];
    $title = $_POST['title'];
    $date = $_POST['date'];
    $details = $_POST['details'];

    if($id != ""){
        foreach($events as &$e){
            if($e['id'] == $id){
                $e['title'] = $title;
                $e['date'] = $date;
                $e['details'] = $details;
            }
        }
    } else {
        $events[] = [
            'id' => time(),
            'title' => $title,
            'date' => $date,
            'details' => $details
        ];
    }

    saveData($file, $events);
    header("Location: admin_gad_events.php");
    exit();
}

// DELETE
if(isset($_GET['delete'])){
    $id = $_GET['delete'];

    $events = array_filter($events, function($e) use ($id){
        return $e['id'] != $id;
    });

    saveData($file, $events);
    header("Location: admin_gad_events.php");
    exit();
}

// SORT
usort($events, function($a,$b){
    return strtotime($b['date']) - strtotime($a['date']);
});
?>

<!DOCTYPE html>
<html>
<head>
<title>GAD Admin CRUD</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container py-5">

<h2>GAD Monthly Events & Accomplishments</h2>

<button class="btn btn-primary my-3" onclick="openModal()">+ Add Entry</button>

<table class="table table-bordered bg-white">
<tr>
<th>Date</th>
<th>Title</th>
<th>Action</th>
</tr>

<?php foreach($events as $e): ?>
<tr>
<td><?php echo $e['date']; ?></td>
<td><?php echo htmlspecialchars($e['title']); ?></td>
<td>
<button onclick='openModal(<?php echo json_encode($e); ?>)' class="btn btn-sm btn-warning">Edit</button>
<a href="?delete=<?php echo $e['id']; ?>" class="btn btn-sm btn-danger">Delete</a>
</td>
</tr>
<?php endforeach; ?>

</table>

</div>

<!-- MODAL -->
<div class="modal fade" id="modal">
<div class="modal-dialog">
<form method="POST" class="modal-content">

<div class="modal-header">
<h5 id="modalTitle">Add Entry</h5>
</div>

<div class="modal-body">

<input type="hidden" name="id" id="id">

<input name="title" id="title" class="form-control mb-2" placeholder="Title" required>
<input type="date" name="date" id="date" class="form-control mb-2" required>
<textarea name="details" id="details" class="form-control" placeholder="Details" required></textarea>

</div>

<div class="modal-footer">
<button type="submit" name="save" class="btn btn-success">Save</button>
</div>

</form>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
function openModal(data=null){
    document.querySelector("form").reset();

    if(data){
        modalTitle.innerText="Edit Entry";
        id.value=data.id;
        title.value=data.title;
        date.value=data.date;
        details.value=data.details;
    } else {
        modalTitle.innerText="Add Entry";
        id.value="";
    }

    new bootstrap.Modal(document.getElementById('modal')).show();
}
</script>

</body>
</html>