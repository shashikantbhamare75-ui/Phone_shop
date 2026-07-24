<?php
session_start();

 require_once __DIR__ . '/includes/session.php'; 
 require_once __DIR__ . '/includes/functions.php'; 

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    // echo "User ID = " . $id;
}

if(isset($_POST['submit'])){
 
// echo"enter data"; 
$user_id = $_SESSION['user_id'];
$username = ucfirst($_SESSION['name']);
$email=$_POST['email'];
$review = $_POST['review'];
$rating = $_POST['rating'];

$stmt = $pdo->prepare("
    INSERT INTO reviews (user_id, username,email, review, rating)
    VALUES (?, ?, ?, ?, ?)
");

$stmt->execute([
    $user_id,
    $username,
    $email,
    $review,
    $rating
]);
}
 require_once __DIR__ . '/includes/header.php'; 
 require_once __DIR__ . '/includes/navbar.php'; 
?>
<!DOCTYPE html>
<html>
    <style>/* Review Form */

/* Review Form Container */

.review-form{
    width: 60%;
    margin: 40px auto;
    background: #1E293B;
    padding: 30px;
    border-radius: 15px;
    box-shadow:
    0 12px 30px rgba(0, 0, 0, 0.35),
    0 4px 12px rgba(59, 130, 246, 0.15);
}

/* Form Groups */

.review-form .form-group{
    margin-bottom: 20px;
}

/* Labels */

.review-form label{
    display: block;
    font-size: 18px;
    font-weight: bold;
    color: #f6f3f3;
    margin-bottom: 8px;
}

/* Text Fields */

.review-form input[type="text"],
.review-form input[type="email"],
.review-form textarea{
    width: 100%;
    padding: 12px 15px;
    font-size: 16px;
    border: 1px solid #ccc;
    border-radius: 8px;
    outline: none;
    transition: 0.3s;
}

.review-form input[type="text"]:focus,
.review-form input[type="email"]:focus,
.review-form textarea:focus{
    border-color: #2563EB;
    box-shadow: 0 0 6px rgba(37,99,235,.3);
}

/* Textarea */

.review-form textarea{
    resize: none;
}

/* Radio Buttons */

/* .review-form input[type="radio"]{
    margin-right: 8px;
    transform: scale(1.2);
    cursor: pointer;
    background:transparent; 
    border:2px solid #f3d780;
    box-shadow: 0 6px 18px rgba(243, 215, 128, 0.35);
} */
/* Radio Button */
.review-form input[type="radio"] {
    appearance: none;
    width: 18px;
    height: 18px;
    border: 2px solid #f3d780;
    border-radius: 50%;
    background: #1E293B;
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
    vertical-align: middle;
}

/* Checked State */
.review-form input[type="radio"]:checked {
    box-shadow: 0 0 10px rgba(243,215,128,0.6);
}

.review-form input[type="radio"]:checked::before {
    content: "";
    position: absolute;
    width: 8px;
    height: 8px;
    background: #f3d780;
    border-radius: 50%;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
}

/* Label */
.review-form input[type="radio"] + label {
    display: inline-block;
    color: #fff;
    font-size: 18px;
    margin: 0 20px 0 8px;
    cursor: pointer;
    vertical-align: middle;
    transition: color 0.3s ease;
}

/* Highlight label when selected */
.review-form input[type="radio"]:checked + label {
    color: #f3d780;
    font-weight: 600;
}

/* Submit Button */

.review-form .submit-btn{
    background: #2563EB;
    color: white;
    border: none;
    padding: 12px 30px;
    font-size: 18px;
    border-radius: 8px;
    cursor: pointer;
    transition: .3s;
}

.review-form .submit-btn:hover{
    background: #1D4ED8;
}

/* Responsive */

@media(max-width:768px){

    .review-form{
        width:95%;
        padding:20px;
    }

}
</style>
<body>
    <div class="review-form">
    <form action="review.php" method="post">
          <div class="form-group">
            <label>Full Name</label>
            <input style="background:transparent; border:2px solid #f3d780;box-shadow: 0 6px 18px rgba(243, 215, 128, 0.35);"
            type="text" name="name" required>
        </div>

        <div class="form-group">
            <label>Email</label>
            <input style="background:transparent; border:2px solid #f3d780;box-shadow: 0 6px 18px rgba(243, 215, 128, 0.35);"
             type="email" name="email" required>
        </div>

        <div class="form-group">
            <label>Product Name</label>
            <input style="background:transparent; border:2px solid #f3d780;box-shadow: 0 6px 18px rgba(243, 215, 128, 0.35);"
             type="text" name="product" required>
        </div>

        <div class="form-group">
            <label>Rating</label>
         <div class="form-group">
    <input type="radio" id="rate5" name="rating" value="5" required>
    <label for="rate5">⭐⭐⭐⭐⭐ </label><br><br>

    <input type="radio" id="rate4" name="rating" value="4">
    <label for="rate4">⭐⭐⭐⭐</label><br><br>

    <input type="radio" id="rate3" name="rating" value="3">
    <label for="rate3">⭐⭐⭐ </label><br><br>

    <input type="radio" id="rate2" name="rating" value="2">
    <label for="rate2">⭐⭐ </label><br><br>

    <input type="radio" id="rate1" name="rating" value="1">
    <label for="rate1">⭐ </label>
</div>
        </div>
<br>
        <div class="form-group">
            <label>Your Review</label>
            <textarea style="background:transparent; border:2px solid #f3d780;box-shadow: 0 6px 18px rgba(243, 215, 128, 0.35);"
             name="review" rows="5" placeholder="Write your experience..." required></textarea>
        </div>

        <button class="submit-btn" name="submit"type="submit">
            Submit Review
        </button>

    </form>
</div>
</body>
</html>
<?php require_once __DIR__ . '/includes/footer.php'; ?>