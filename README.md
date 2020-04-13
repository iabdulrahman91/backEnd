# backEnd

## APIs
##Auth
###**POST
#####/login
````
Request:
{
	"email": "a@a.com",
	"password": "myPassword"
}

Response
{
    "user": {
        "id": 1,
        "fname": "abdulrahman",
        "lname": "alanazi",
        "email": "a@a.com",
        "phone": "0500000000"
    },
    "token": "eyJ0eXTokenTokenTokenTokenTokenTokenTokenToken"
}
````
#####/register
````
Request:
{
	"fname": "Abdulrahman",
	"lname": "Mubarak",
	"email": "b@a.com",
	"phone": "0500000000",
	"password": "myPassword",
	"c_password": "myPassword"
}

Response
{
    "user": {
        "id": 1,
        "fname": "abdulrahman",
        "lname": "alanazi",
        "email": "a@a.com",
        "phone": "0500000000"
    },
    "token": "eyJ0eXTokenTokenTokenTokenTokenTokenTokenToken"
}
````
#####/details
````
header 'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0a
````
##Listing
###**GET
#####/listings
#####/listings/{id}
###**POST
#####/listings
````
Request
{
    "location": 6,
    "item": jsonEncode({
        "type": "Equipment",
        "company": "Cannon",
        "category": "Body",
        "model": "D5000"
    }),
    "price": 44.55,
    "days": jsonEncode([
        "02-12-2019",
        "03-12-2019",
        "4-12-2019",
        "22-12-8123",
        "23-12-8123"
    ])
}

Response
{
    "data": {
        "id": 3,
        "user_id": 1,
        "location": {
            "id": 6,
            "region_id": 2,
            "name_ar": "مكة المكرمة",
            "name_en": "Makkah Al Mukarramah",
            "center": "[21.42717994, 39.84349001]",
            "created_at": "2019-10-17 07:51:24",
            "updated_at": "2019-10-17 07:51:24"
        },
        "deliverable": false,
        "item": {
            "type": "Equipment",
            "company": "Cannon",
            "category": "Body",
            "model": "D5000"
        },
        "days": {
            "02-12-2019": 1,
            "03-12-2019": 1,
        },
        "price": "44.55",
        "active": true
    }
}
````