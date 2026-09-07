// Initialization for ES Users
import { Ripple, initMDB } from "mdb-ui-kit";

initMDB({ Ripple });

/*
function followUser(followee_id) {
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "follow.php", true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            alert(xhr.responseText);
            location.reload(); // Reload page to update follow status
        }
    };
    xhr.send("followee_id=" + followee_id);
}

function unfollowUser(followee_id) {
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "unfollow.php", true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            alert(xhr.responseText);
            location.reload(); // Reload page to update follow status
        }
    };
    xhr.send("followee_id=" + followee_id);
}
*/
