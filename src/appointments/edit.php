<?php
session_start();
require_once '../includes/db.php';
$activePage = 'appointments';
$errors = [];

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM appointmenttb WHERE id = ?");
$stmt->execute([$id]);
$appt = $stmt->fetch();
if (!$appt) { $_SESSION['flash'] = ['type'=>'danger','msg'=>'Appointment not found.']; header('Location: /hospital/appointments/index.php'); exit; }

$doctors  = $pdo->query("SELECT id, name, specialization FROM doctb ORDER BY name")->fetchAll();
$patients = $pdo->query("SELECT id, fname, lname FROM patreg ORDER BY fname, lname")->fetchAll();

$form = [
    'pid' => $appt['pid'],
    'did'  => $appt['did'],
    'apdate'    => substr($appt['apdate'], 0, 10),
    'aptime'    => substr($appt['aptime'], 0, 5),
    'status'     => $appt['status'],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form = [
        'pid' => (int)($_POST['pid'] ?? 0),
        'did'  => (int)($_POST['did']  ?? 0),
        'apdate'    => trim($_POST['apdate']  ?? ''),
        'aptime'    => trim($_POST['aptime']  ?? ''),
        'status'     => trim($_POST['status']   ?? 'Pending'),
    ];
    if (!$form['pid']) $errors['pid'] = 'Please select a patient.';
    if (!$form['did'])  $errors['did']  = 'Please select a doctor.';
    if (!$form['apdate'])    $errors['apdate']     = 'Appointment date is required.';
    if (!$form['aptime'])    $errors['aptime']     = 'Appointment time is required.';
    if (!in_array($form['status'], ['Pending','Confirmed','Completed','Cancelled'])) $errors['status'] = 'Invalid status.';

    // Conflict detection — exclude current appointment
    if (empty($errors)) {
        $conflict = $pdo->prepare("SELECT COUNT(*) FROM appointmenttb WHERE did=? AND apdate=? AND aptime=? AND status IN ('Pending','Confirmed') AND id != ?");
        $conflict->execute([$form['did'], $form['apdate'], $form['aptime'], $id]);
        if ($conflict->fetchColumn()) $errors['aptime'] = 'That doctor already has a Pending or Confirmed appointment at this date and time.';
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("UPDATE appointmenttb SET pid=?, did=?, apdate=?, aptime=?, status=? WHERE id=?");
        $stmt->execute([$form['pid'], $form['did'], $form['apdate'], $form['aptime'], $form['status'], $id]);
        $_SESSION['flash'] = ['type'=>'success','msg'=>'Appointment updated successfully!'];
        header('Location: /hospital/appointments/index.php'); exit;
    }
}

require_once '../includes/header.php';
?>
<div class="card">
  <div class="card-header">
    <div>
      <div class="card-title" style="color:#0d6efd;"><i class="fa-solid fa-calendar-pen me-2"></i>Edit Appointment</div>
      <div class="card-subtitle">Update appointment details below.</div>
    </div>
    <a href="/hospital/src/appointments/index.php" class="btn btn-sm btn-light"><i class="fa-solid fa-arrow-left me-1"></i> Back</a>
  </div>
  <div class="card-body p-4">
    <form method="POST" novalidate>
      <div class="row g-3">

        <div class="col-md-6">
          <label class="form-label">Patient <span class="text-danger">*</span></label>
          <div class="input-group">
            <span class="input-group-text"><i class="fa-solid fa-hospital-user text-muted"></i></span>
            <select name="pid" class="form-select <?= isset($errors['pid'])?'is-invalid':'' ?>">
              <option value="">-- Select Patient --</option>
              <?php foreach ($patients as $p): ?>
              <option value="<?= $p['id'] ?>" <?= $form['pid']==$p['id']?'selected':'' ?>><?= htmlspecialchars($p['fname'].' '.$p['lname']) ?></option>
              <?php endforeach; ?>
            </select>
            <?php if (isset($errors['pid'])): ?><div class="invalid-feedback"><?= $errors['pid'] ?></div><?php endif; ?>
          </div>
        </div>

        <div class="col-md-6">
          <label class="form-label">Doctor <span class="text-danger">*</span></label>
          <div class="input-group">
            <span class="input-group-text"><i class="fa-solid fa-user-doctor text-muted"></i></span>
            <select name="did" class="form-select <?= isset($errors['did'])?'is-invalid':'' ?>">
              <option value="">-- Select Doctor --</option>
              <?php foreach ($doctors as $d): ?>
              <option value="<?= $d['id'] ?>" <?= $form['did']==$d['id']?'selected':'' ?>><?= htmlspecialchars($d['name'].' ('.$d['specialization'].')') ?></option>
              <?php endforeach; ?>
            </select>
            <?php if (isset($errors['did'])): ?><div class="invalid-feedback"><?= $errors['did'] ?></div><?php endif; ?>
          </div>
        </div>

        <div class="col-md-6">
          <label class="form-label">Appointment Date <span class="text-danger">*</span></label>
          <div class="input-group">
            <span class="input-group-text"><i class="fa-solid fa-calendar text-muted"></i></span>
            <input type="date" name="apdate" class="form-control <?= isset($errors['apdate'])?'is-invalid':'' ?>" value="<?= htmlspecialchars($form['apdate']) ?>">
            <?php if (isset($errors['apdate'])): ?><div class="invalid-feedback"><?= $errors['apdate'] ?></div><?php endif; ?>
          </div>
        </div>

        <div class="col-md-6">
          <label class="form-label">Appointment Time <span class="text-danger">*</span></label>
          <div class="input-group">
            <span class="input-group-text"><i class="fa-solid fa-clock text-muted"></i></span>
            <input type="time" name="aptime" class="form-control <?= isset($errors['aptime'])?'is-invalid':'' ?>" value="<?= htmlspecialchars($form['aptime']) ?>">
            <?php if (isset($errors['aptime'])): ?><div class="invalid-feedback"><?= $errors['aptime'] ?></div><?php endif; ?>
          </div>
        </div>

        <div class="col-md-6">
          <label class="form-label">Status</label>
          <div class="input-group">
            <span class="input-group-text"><i class="fa-solid fa-circle-dot text-muted"></i></span>
            <select name="status" class="form-select">
              <?php foreach (['Pending','Confirmed','Completed','Cancelled'] as $s): ?>
              <option value="<?= $s ?>" <?= $form['status']===$s?'selected':'' ?>><?= $s ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

      </div>
      <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
        <a href="/hospital/appointments/index.php" class="btn btn-light">Cancel</a>
        <button type="submit" class="btn btn-primary px-4"><i class="fa-solid fa-floppy-disk me-1"></i> Update Appointment</button>
      </div>
    </form>
  </div>
</div>
<?php require_once '../includes/footer.php'; ?>
