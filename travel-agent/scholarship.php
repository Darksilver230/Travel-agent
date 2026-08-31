<?php
/**
 * scholarship.php
 * ----------------
 * Shows full details for ONE scholarship (identified by ?id=..) and
 * the application form. Anyone can view the scholarship, but you must
 * be logged in to actually apply.
 */
require 'includes/db.php';
require 'includes/auth.php';
include 'includes/header.php';

$id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare("
    SELECT scholarships.*, universities.name AS university_name, universities.country
    FROM scholarships
    JOIN universities ON scholarships.university_id = universities.id
    WHERE scholarships.id = ? AND scholarships.is_active = 1
");
$stmt->execute([$id]);
$sch = $stmt->fetch();

if (!$sch) {
    echo '<div class="container"><p>Scholarship not found.</p><a href="universities.php">Back to all scholarships</a></div>';
    include 'includes/footer.php';
    exit;
}

$applicationError = $_GET['error'] ?? null;
$user = current_user($pdo);
?>

<section class="container scholarship-detail page-top">
    <a href="universities.php" class="back-link">&larr; Back to all scholarships</a>

    <div class="scholarship-detail-grid">
        <img src="<?php echo htmlspecialchars($sch['image_url']); ?>" alt="<?php echo htmlspecialchars($sch['title']); ?>">

        <div>
            <h1><?php echo htmlspecialchars($sch['title']); ?></h1>
            <p class="muted"><?php echo htmlspecialchars($sch['university_name'] . ', ' . $sch['country']); ?></p>
            <p><?php echo htmlspecialchars($sch['description']); ?></p>
            <ul class="detail-list">
                <li><strong>Duration:</strong> <?php echo $sch['duration_months']; ?> months</li>
                <li><strong>Max Applicants per Application:</strong> <?php echo $sch['max_applicants']; ?></li>
                <li><strong>Deadline:</strong> <?php echo $sch['deadline_from']; ?> to <?php echo $sch['deadline_to']; ?></li>
                <li><strong>Amount:</strong> $<?php echo number_format($sch['amount'], 2); ?></li>
            </ul>
        </div>
    </div>

    <div class="application-box">
        <h2>Apply Now</h2>

        <?php if ($applicationError): ?>
            <p class="alert error"><?php echo htmlspecialchars($applicationError); ?></p>
        <?php endif; ?>

        <?php if (!$user): ?>
            <p class="alert">
                You need an account to apply for scholarships.
                <a href="login.php?redirect=<?php echo urlencode('scholarship.php?id=' . $sch['id']); ?>">Log in</a>
                or
                <a href="register.php?redirect=<?php echo urlencode('scholarship.php?id=' . $sch['id']); ?>">create one</a>
                — it only takes a minute.
            </p>
        <?php else: ?>
            <p class="muted">Applying as <?php echo htmlspecialchars($user['full_name']); ?> (<?php echo htmlspecialchars($user['email']); ?>)</p>

            <form action="process_application.php" method="POST" class="application-form">
                <input type="hidden" name="scholarship_id" value="<?php echo $sch['id']; ?>">

                <div class="field">
                    <label for="deadline_date">Preferred Start Date</label>
                    <input type="date" id="deadline_date" name="deadline_date"
                           min="<?php echo $sch['deadline_from']; ?>"
                           max="<?php echo $sch['deadline_to']; ?>" required>
                </div>
                <div class="field">
                    <label for="num_applicants">Number of Applicants</label>
                    <input type="number" id="num_applicants" name="num_applicants"
                           min="1" max="<?php echo $sch['max_applicants']; ?>" value="1" required>
                </div>
                <div class="field full-width">
                    <label for="special_requests">Additional Information (optional)</label>
                    <textarea id="special_requests" name="special_requests" rows="3"></textarea>
                </div>

                <button type="submit">Continue to Payment</button>
            </form>
        <?php endif; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
