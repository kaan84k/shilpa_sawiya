<?php
// admin_render.php

function renderUnverifiedUsers($unverified_users) {
    if (count($unverified_users) === 0) {
        echo '<tr><td colspan="4" style="padding:1rem; text-align:center; color:#888;">No unverified users to verify.</td></tr>';
    } else {
        foreach ($unverified_users as $user) {
            echo '<tr>';
            echo '<td style="padding:0.5rem 1rem; border-bottom:1px solid #f1f1f1;">' . htmlspecialchars($user['name']) . '</td>';
            echo '<td style="padding:0.5rem 1rem; border-bottom:1px solid #f1f1f1;">' . htmlspecialchars($user['email']) . '</td>';
            echo '<td style="padding:0.5rem 1rem; border-bottom:1px solid #f1f1f1;">' . htmlspecialchars($user['mobile']) . '</td>';
            echo '<td style="padding:0.5rem 1rem; border-bottom:1px solid #f1f1f1;">';
            echo '<form method="POST" style="display:inline; margin-right:0.5rem;" onsubmit="return confirm(\'Are you sure you want to verify this user?\');">';
            echo '<input type="hidden" name="verify_user_id" value="' . $user['id'] . '">';
            echo '<button type="submit" class="btn btn-success" style="background:#198754; color:#fff; border:none; padding:0.35rem 1.1rem; border-radius:3px; cursor:pointer; font-weight:500; font-size:0.97rem;">Verify</button>';
            echo '</form>';
            echo '<button type="button" class="btn btn-info view-user-btn" 
                data-name="' . htmlspecialchars(addslashes($user['name'])) . '" 
                data-email="' . htmlspecialchars(addslashes($user['email'])) . '" 
                data-mobile="' . htmlspecialchars(addslashes($user['mobile'])) . '"';
            $profile_picture = '';
            $profile_picture_path = '../uploads/profile_pictures/user_' . $user['id'] . '_*.jpg';
            $profile_picture_files = glob($profile_picture_path);
            if (!$profile_picture_files) {
                $profile_picture_path = '../uploads/profile_pictures/user_' . $user['id'] . '_*.png';
                $profile_picture_files = glob($profile_picture_path);
            }
            if ($profile_picture_files && count($profile_picture_files) > 0) {
                $profile_picture = $profile_picture_files[0];
            }
            if ($profile_picture) {
                echo ' data-picture="' . htmlspecialchars($profile_picture) . '"';
            } else {
                echo ' data-picture=""';
            }
            echo '>View</button>';
            echo '</td>';
            echo '</tr>';
        }
    }
}

function renderRecentDonations($recent_donations) {
    if (count($recent_donations) === 0) {
        echo '<tr><td colspan="4" style="padding:1rem; text-align:center; color:#888;">No available donations found.</td></tr>';
    } else {
        foreach ($recent_donations as $donation) {
            echo '<tr>';
            echo '<td style="padding:0.5rem 1rem; border-bottom:1px solid #f1f1f1;">' . htmlspecialchars($donation['title']) . '</td>';
            echo '<td style="padding:0.5rem 1rem; border-bottom:1px solid #f1f1f1;">' . htmlspecialchars($donation['category']) . '</td>';
            echo '<td style="padding:0.5rem 1rem; border-bottom:1px solid #f1f1f1;">' . date('Y-m-d', strtotime($donation['created_at'])) . '</td>';
            echo '<td style="padding:0.5rem 1rem; border-bottom:1px solid #f1f1f1;">';
            echo '<button class="btn btn-info view-donation-btn" 
                data-title="' . htmlspecialchars(addslashes($donation['title'])) . '" 
                data-category="' . htmlspecialchars(addslashes($donation['category'])) . '" 
                data-date="' . date('Y-m-d', strtotime($donation['created_at'])) . '" 
                data-description="' . htmlspecialchars(addslashes($donation['description'])) . '">View</button>';
            echo '</td>';
            echo '</tr>';
        }
    }
}
