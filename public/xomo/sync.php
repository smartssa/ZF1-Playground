<?php
/*
 * sync a list of events given by the mobile app
 * 
{
    "UserDoc": {
        "fbid": "11112222333",
        "schedule": [
            {
                "id": "22",
                "timestamp": "1330551329",
                "active": "1"
            },
            {
                "id": "24",
                "timestamp": "1330551321",
                "active": "0"
            }
        ],
        "event": [
            {
                "id": "26632",
                "timestamp": "1330551229",
                "active": "1"
            },
            {
                "id": "23364",
                "timestamp": "1330551221",
                "active": "1"
            }
        ]
    }
}
*
* return a list of current events for the user
*/

// pull the users' active list
// merge/remove/whatnot
// save the new list
// voila! magic.
// return the existing list back to the mobile app