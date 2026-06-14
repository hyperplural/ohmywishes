# Ohmywishes API Appendix

This document is a working appendix extracted from observed requests and responses.
The main documentation is split into:

- [`docs/v2.md`](v2.md)
- [`docs/v3.md`](v3.md)
- [`docs/public.md`](public.md)

This appendix keeps field-level observations, normalization notes, and DTO candidates.

## Base

- Host: `https://ohmywishes.com`
- API versions observed: `v2`, `v3`
- Auth:
  - `x-access-token: <JWT>`
  - session cookie `token=<JWT>` was present in browser requests
- Region header observed:
  - `x-content-region: russia`
- Content type:
  - `application/json`

## Conventions

- IDs are string values, typically 24 hex chars.
- Date values are ISO 8601 strings.
- Some endpoints return wrapped payloads like `{ item: ... }` or `{ items: [...] }`.
- The same entity can have different field names depending on endpoint:
  - `fullname` vs `fullName`
  - `photo` vs `avatar`
  - `bio` vs `about`
  - `lastName` vs `lastname`
  - `firstName` vs `firstname`

## Authenticated User

### `GET /api/v2/users/self`

Returns the current authenticated user profile.

Observed response:

```json
{
  "_id": "bb677c0ecd14900917853653",
  "username": "dream",
  "fullName": "vasily ",
  "photo": "/s3/images/user-avatar/2026/06/14/1Cc3KHPzP6XTZPGLce1sTJ.webp",
  "avatar": {
    "width": 267,
    "height": 400,
    "url": "https://cdn.ohmywishes.com/images/user-avatar/2026/06/14/1Cc3KHPzP6XTZPGLce1sTJ.webp",
    "thumbnails": [
      {
        "width": 53,
        "height": 80,
        "url": "https://cdn.ohmywishes.com/images/thumbnail/2026/06/14/1Cc3KHTg8dmFAa5jAD2bde.webp"
      }
    ]
  }
}
```

### `PUT /api/v2/users/self`

Updates the current user profile.

Request body:

```json
{
  "username": "dream",
  "firstName": "vasily",
  "lastName": "",
  "bio": "",
  "sex": "",
  "birthday": null
}
```

Observed response:

```json
{
  "_id": "bb677c0ecd14900917853653",
  "username": "dream",
  "fullName": "vasily ",
  "photo": "/s3/images/user-avatar/2026/06/14/1Cc3KHPzP6XTZPGLce1sTJ.webp",
  "avatar": {
    "width": 267,
    "height": 400,
    "url": "https://cdn.ohmywishes.com/images/user-avatar/2026/06/14/1Cc3KHPzP6XTZPGLce1sTJ.webp",
    "thumbnails": [
      {
        "width": 53,
        "height": 80,
        "url": "https://cdn.ohmywishes.com/images/thumbnail/2026/06/14/1Cc3KHTg8dmFAa5jAD2bde.webp"
      },
      {
        "width": 107,
        "height": 160,
        "url": "https://cdn.ohmywishes.com/images/thumbnail/2026/06/14/1Cc3KHStVwqNEN1CNFvTw5.webp"
      }
    ]
  },
  "bio": "",
  "birthday": null,
  "sex": null,
  "systemNewsActive": false,
  "newsActive": false,
  "email": "alexei.petrov@example.com",
  "isEmailConfirmed": true,
  "lastName": "",
  "firstName": "vasily",
  "isPro": false,
  "accountType": "user",
  "socialProfiles": ["yandex"],
  "followersCount": 1,
  "followingsCount": 1,
  "wishesCount": 11,
  "assignedWishesBySomebody": 0
}
```

DTO candidates:

- `UpdateSelfUserRequestDto`
- `SelfUserDto`
- `AvatarDto`
- `AvatarThumbnailDto`

### `POST /api/v2/users/self/wishes`

Creates a new wish for the authenticated user.

Request body observed:

```json
{
  "title": "тестик",
  "description": null,
  "link": null,
  "price": null,
  "currency": "RUB",
  "private": true,
  "wish_lists": [],
  "is_multi_reservation_available": true
}
```

Observed response shape was not captured, but the endpoint accepts writable
privacy and multi-reservation flags.

DTO candidates:

- `CreateWishRequestDto`
- `WishReservationSettingsDto`

Notes:

- `private` is writable.
- `is_multi_reservation_available` is writable.
- `price` can be `null`.
- `link` can be `null`.

### `POST /api/v2/users/self/wishes/{wishId}/picture`

Uploads a picture for a wish via `multipart/form-data`.

Multipart fields observed:

- `picture`: binary image file

Observed response:

```json
{
  "_id": "346a2edca32a5c2744383491",
  "title": "о",
  "photo": "/s3/images/wish-photo/2026/06/14/1Cc3W2nXp9V46i3KmPqBPh.webp",
  "photos": [
    {
      "width": 1000,
      "height": 1000,
      "url": "https://cdn.ohmywishes.com/images/wish-photo/2026/06/14/1Cc3W2nXp9V46i3KmPqBPh.webp",
      "thumbnails": []
    }
  ]
}
```

DTO candidates:

- `WishDto`

Notes:

- The endpoint returns the updated wish object.
- The request uses multipart upload, not JSON.

### `POST /api/v2/users/self/avatars`

Uploads a profile avatar via `multipart/form-data`.

Multipart fields observed:

- `picture`: binary image file

Observed response:

```json
{
  "_id": "bb677c0ecd14900917853653",
  "username": "dream",
  "photo": "/s3/images/user-avatar/2026/06/14/1Cc3WAKvsy35ULZ48jEw9X.webp",
  "avatar": {
    "width": 564,
    "height": 846,
    "url": "https://cdn.ohmywishes.com/images/user-avatar/2026/06/14/1Cc3WAKvsy35ULZ48jEw9X.webp"
  }
}
```

DTO candidates:

- `SelfUserDto`

Notes:

- The endpoint returns the updated profile object.
- The request uses multipart upload, not JSON.

### `DELETE /api/v2/users/self/wishes/{wishId}`

Deletes an owned wish.

Observed response:

- HTTP `204 No Content`

DTO candidates:

- no body handling needed

Notes:

- `fullName` looks derived from first/last name, not a primary stored field.
- `sex` accepts an empty string in the request but returns `null`.
- `birthday` can be `null`.

## Followings

### `GET /api/v3/users/{userId}/followings?limit=20&offset=0`

Returns users the given user is following.

Observed response:

```json
{
  "items": [
    {
      "id": "ba63dbb5c7c0cc6124659619",
      "username": "milkh8er",
      "avatar": {
        "url": "https://cdn.ohmywishes.com/images/user-avatar/2025/12/10/1CVxu38uFMhHPk1yeaEitx20254.webp",
        "width": 300,
        "height": 400,
        "thumbnails": [
          {
            "url": "https://cdn.ohmywishes.com/images/thumbnail/2025/12/10/1CVxu3KHqX1J3piecVkVe5.webp",
            "width": 60,
            "height": 80
          },
          {
            "url": "https://cdn.ohmywishes.com/images/thumbnail/2025/12/10/1CVxu3JJZ5ygnAiDK3VCsV.webp",
            "width": 120,
            "height": 160
          }
        ]
      },
      "fullname": "tasha",
      "accountType": "user",
      "isFollowedByMe": true,
      "isInMyFavorite": false,
      "isFollowingRequestSent": false,
      "birthday": {
        "next": "2026-12-22",
        "daysLeft": null
      }
    }
  ]
}
```

Query parameters observed:

- `limit` integer
- `offset` integer
- `query` string, optional search term

DTO candidates:

- `UserListItemDto`
- `UserAvatarDto`
- `BirthdayNextDto`
- `UsersPageDto`

Notes:

- `fullname` is lowercase in this endpoint, while other endpoints use `fullName`.
- `birthday.daysLeft` may be `null`.

### `POST /api/v3/users/{userId}/following`

Starts following a user.

Observed response:

- HTTP `201 Created`
- response body: `null`

### `DELETE /api/v3/users/{userId}/following`

Stops following a user.

Observed response:

- HTTP `204 No Content`

Notes:

- This is separate from the legacy `favorite` endpoints.
- The follow relationship has dedicated status flags in user list/profile payloads.

### `GET /api/v3/users/{userId}/followers?limit=20&offset=0`

Returns followers of the given user.

Shape is the same as followings in the observed sample.

DTO candidates:

- reuse `UserListItemDto`
- reuse `UsersPageDto`

### `GET /api/v3/own-user/followers/requests`

Returns pending follower requests for the current user.

Observed response:

```json
{
  "items": []
}
```

When non-empty, items use the same user item shape as follower/following lists.

DTO candidates:

- reuse `UserListItemDto`
- `FollowerRequestsDto`

Notes:

- This endpoint likely represents incoming follow requests.

## Own User Settings

### `GET /api/v3/own-user/notifications/settings`

Returns notification preferences for the current user.

Observed response:

```json
{
  "item": {
    "sending": {
      "myNewWish": true,
      "myBirthday": true
    },
    "receiving": {
      "push": {
        "myWishReservation": true,
        "newFollower": true,
        "followingsNewWish": true,
        "soonBirthdayFollowingsNewWish": false,
        "followingsBirthday": true,
        "followingsTodayBirthday": true,
        "secretSantaGamesEvents": true,
        "myFollowingRequest": true,
        "myReservation": true,
        "myBirthday": true,
        "followingBrandsNewIdea": true,
        "followingInfluencersNewIdea": true,
        "updateWishlistOnHoliday": false,
        "reserveWishesOnHoliday": false,
        "selectionsNewIdea": false,
        "serviceNews": false
      },
      "email": {
        "myWishReservation": false,
        "newFollower": false,
        "followingsNewWish": false,
        "soonBirthdayFollowingsNewWish": false,
        "followingsBirthday": false,
        "followingsTodayBirthday": false,
        "secretSantaGamesEvents": false,
        "myFollowingRequest": false,
        "myReservation": false,
        "myBirthday": false,
        "followingBrandsNewIdea": false,
        "followingInfluencersNewIdea": false,
        "updateWishlistOnHoliday": false,
        "reserveWishesOnHoliday": false,
        "selectionsNewIdea": false,
        "serviceNews": false
      }
    }
  }
}
```

DTO candidates:

- `NotificationSettingsResponseDto`
- `NotificationSendingSettingsDto`
- `NotificationReceivingSettingsDto`
- `NotificationChannelSettingsDto`

Notes:

- `sending` and `receiving` are separated.
- `receiving` is split by channel: `push` and `email`.
- Flags are booleans.

### `PUT /api/v3/own-user/settings`

Updates account and privacy flags for the current user.

Request body observed:

```json
{
  "isReceiveNewsAllowed": false,
  "isReceiveSystemNewsAllowed": false,
  "isPrivateWishesBlurred": false,
  "isReservedWishesCounterEnabled": true,
  "isSecretSantaAllYearAvailable": false,
  "isPrivateProfile": true,
  "isSubscriptionApprovalRequired": true
}
```

Observed response:

- HTTP `204 No Content`

DTO candidates:

- `UpdateOwnUserSettingsRequestDto`
- `OwnUserSettingsDto`

Notes:

- `isSubscriptionApprovalRequired` controls manual approval for followers.
- `isPrivateProfile` controls private profile mode.
- `isReceiveNewsAllowed` and `isReceiveSystemNewsAllowed` are notification toggles.
- `isPrivateWishesBlurred` and `isReservedWishesCounterEnabled` are UI/privacy toggles.
- `isSecretSantaAllYearAvailable` is a feature toggle.

### `POST /api/v3/own-user/email/confirmation-code`

Requests an email change confirmation link to be sent to a new email address.

Request body observed:

```json
{
  "email": "login.bot@example.net"
}
```

Observed response:

- `null`
- user-facing message: "We sent you a secret link to the email address. Follow it to change the email address."

DTO candidates:

- `RequestEmailChangeCodeDto`

Notes:

- The endpoint appears to trigger an email with a secret link.
- No structured success body was returned.

### `PUT /api/v3/own-user/email`

Confirms an email change using a code from the email flow.

Request body observed:

```json
{
  "confirmationCode": "9587",
  "email": "login.bot@example.net"
}
```

Observed response:

- HTTP `204 No Content`

DTO candidates:

- `ConfirmEmailChangeRequestDto`

Notes:

- This endpoint finalizes the email change.
- The confirmation code is required together with the target email.

## Auth

### `POST /api/v3/auth/email/confirmation-code`

Requests a login confirmation code for email authentication.

Request body observed:

```json
{
  "email": "login.bot@example.net",
  "captchaToken": "turnstile:..."
}
```

Observed response:

- HTTP `202 Accepted`
- body: `null`

DTO candidates:

- `RequestAuthEmailCodeDto`

Notes:

- `captchaToken` is required in the observed flow.
- The backend likely sends an email code.

### `POST /api/v3/auth/email/login`

Completes email login using the confirmation code.

Request body observed:

```json
{
  "email": "login.bot@example.net",
  "confirmationCode": "1566"
}
```

Observed response:

```json
{
  "item": {
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9....",
    "expiresIn": 31536000
  }
}
```

DTO candidates:

- `AuthTokenResponseDto`
- `AuthTokenDto`

Notes:

- This is a passwordless email login flow.
- `expiresIn` appears to be seconds.
- The returned `token` is a bearer/session token and should not be persisted in docs or logs.

## Favorites

### `POST /api/v2/users/{userId}/favorite`

Adds a user to favorites.

Observed response:

```json
{}
```

### `DELETE /api/v2/users/{userId}/favorite`

Removes a user from favorites.

Observed response:

- HTTP `204 No Content`

DTO candidates:

- `VoidResponseDto` or no body handling

## User Profile

### `GET /api/v3/users/{username}`

Returns public profile information by username.

Observed response:

```json
{
  "item": {
    "id": "bb677c0ecd14900917853653",
    "username": "dream",
    "avatar": {
      "url": "https://cdn.ohmywishes.com/images/user-avatar/2026/06/14/1Cc3KHPzP6XTZPGLce1sTJ.webp",
      "width": 267,
      "height": 400,
      "thumbnails": [
        {
          "url": "https://cdn.ohmywishes.com/images/thumbnail/2026/06/14/1Cc3KHTg8dmFAa5jAD2bde.webp",
          "width": 53,
          "height": 80
        },
        {
          "url": "https://cdn.ohmywishes.com/images/thumbnail/2026/06/14/1Cc3KHStVwqNEN1CNFvTw5.webp",
          "width": 107,
          "height": 160
        }
      ]
    },
    "backgroundImage": null,
    "about": "",
    "lastname": "",
    "firstname": "vasily",
    "fullname": "vasily",
    "followersCount": 1,
    "followingsCount": 1,
    "wishesCount": 11,
    "hasWishesReservedBySomebody": false,
    "isFollowedByMe": false,
    "isFollowingRequestSent": false,
    "isInMyFavorite": false,
    "isSponsor": false,
    "socialProfiles": ["yandex"],
    "isPro": false,
    "accountType": "user",
    "stats": {
      "followersCount": 1,
      "followingsCount": 1,
      "wishesCount": 11,
      "wishListsCount": 5,
      "hasWishesReservedBySomebody": false,
      "wishesCountReservedBySomebody": 0
    },
    "socialProfileLinks": [],
    "isPrivate": false,
    "birthday": null,
    "settings": {
      "isSubscriptionApprovalRequired": false
    }
  }
}
```

DTO candidates:

- `PublicUserProfileResponseDto`
- `PublicUserProfileDto`
- `UserStatsDto`
- `UserSettingsDto`

Notes:

- `about` corresponds semantically to `bio`.
- `lastname` / `firstname` are lowercase here.
- `accountType` can be `user` or `brand`.
- Brand profiles include richer public descriptions and background images.
- `isPro` is `true` for brand/influencer-style accounts in the observed data.
- Nested creator objects in selection/idea payloads can also use `accountType: "influencer"`.

## Selections

### `GET /api/v3/selections`

Returns the homepage selection payload.

Observed response:

```json
{
  "item": {
    "id": "0189ba1b-c5d9-700c-b88e-3ac098148c5f",
    "type": "selection",
    "contentType": "ideas",
    "title": "Для вас",
    "fullTitle": null,
    "slug": "glavnaia",
    "description": "Мы собрали для вас ...",
    "extendedDescription": "Ohmywishes — бесплатный сервис ...",
    "icon": null,
    "isExplicit": false,
    "ideasCount": 241,
    "sortingOrder": 1,
    "selections": [],
    "brands": [],
    "banners": [],
    "ideaLists": [],
    "meta": {
      "title": "Ohmywishes — сервис вишлистов ...",
      "description": "Мы собрали для вас ..."
    }
  }
}
```

DTO candidates:

- `SelectionDto`
- `SelectionMetaDto`
- `SelectionBannerDto`
- `SelectionIdeaListDto`
- `SelectionBrandDto`

Notes:

- Root response is wrapped in `{ item: ... }`.
- Selections can nest other selections.
- A selection may include `brands`, `banners`, and `ideaLists`.

### `GET /api/v3/selections/{selectionId}/ideas?limit=20&offset=0`

Returns ideas for a selection.

Observed response:

```json
{
  "items": [
    {
      "id": "5c690b092aaee9c436415137",
      "trackingId": "5c690b092aaee9c436415137",
      "title": "Колье Аква — Moon Soul",
      "description": "...",
      "price": {
        "price": 5650,
        "currency": "RUB"
      },
      "externalUri": "https://moon-soul.ru/...",
      "photos": [],
      "icon": null,
      "isOneTimeGift": false,
      "isPrivate": false,
      "visibility": "public",
      "isFulfilled": false,
      "creator": null,
      "isCopiedByMe": false,
      "isAssignedBySomeone": false,
      "isAssignedByMe": false,
      "isReservedBySomeone": false,
      "isReservedByMe": false,
      "reservedUntil": null,
      "isIdea": true,
      "isExternalUriBlocked": false,
      "promoCode": null,
      "wishLists": [],
      "isExplicit": false,
      "isForeignAgentIdea": false,
      "actionButtonText": "where_to_buy",
      "canonicalUrl": "https://ohmywishes.com/ideas/5c690b092aaee9c436415137",
      "sticker": null
    }
  ]
}
```

DTO candidates:

- `IdeaDto`
- `IdeaPriceDto`
- `IdeaPromoCodeDto`
- `IdeaCreatorDto`
- `IdeaPhotoDto`
- `IdeasPageDto`

Notes:

- `creator` can be `null`.
- `promoCode` can be `null` or structured.
- `wishLists` is usually empty.

## Wish Lists

### `GET /api/v3/own-user/wish-lists`

Returns wish lists owned by the current user.

Observed response:

```json
{
  "items": [
    {
      "_id": "a46a2eb6f51a24c184415464",
      "title": "desk",
      "icon": {
        "url": "https://cdn.ohmywishes.com/images/wish-photo/2026/06/14/1Cc3KB8M7de4MEa4tcuvmM.webp",
        "width": 1000,
        "height": 1000,
        "thumbnails": [
          {
            "url": "https://cdn.ohmywishes.com/images/thumbnail/2026/06/14/1Cc3KBEmagh8Vxv6i7m6ep.webp",
            "width": 500,
            "height": 500
          }
        ]
      },
      "fullTitle": null,
      "description": null,
      "slug": "desk",
      "wishesCount": 2,
      "visibility": "inherit",
      "sharedLink": null
    }
  ]
}
```

DTO candidates:

- `WishListDto`
- `WishListIconDto`
- `WishListsPageDto`

Notes:

- `visibility` can be `inherit`, `public`, `followers`, `by_link`, or `private`.
- `fullTitle`, `description`, `sharedLink` can be `null`.

## Wish Aggregates

### `GET /api/v2/users/self/wish-lists/private/wishes?size=20&page=1`

Returns the authenticated user's private wishes.

Observed response shape:

- same denormalized list-style wish array as `GET /api/v2/users/self/wish-lists/all/wishes`

Notes:

- This appears to be the "hidden" or private wishes listing.
- Items should likely keep the same DTO as the general wish list, with `private: true`.

### `GET /api/v2/users/self/wish-lists/reserved/wishes?size=20&page=1`

Returns wishes reserved by the authenticated user.

Observed response shape:

- same denormalized list-style wish array as `GET /api/v2/users/self/wish-lists/all/wishes`

Notes:

- This is the list of wishes the current user has reserved for someone else.
- Items should likely keep the same DTO as the general wish list, with reservation-related fields populated as needed.

### `GET /api/v2/users/self/wish-lists/fulfilled/wishes?size=20&page=1`

Returns wishes marked as fulfilled for the authenticated user.

Observed response:

```json
[
  {
    "_id": "35677c5e7e2b202855025056",
    "trackingId": null,
    "createdAt": "2025-01-06T22:51:42.000Z",
    "updatedAt": "2025-01-06T22:51:42.000Z",
    "title": "iphone 15 pro / apple",
    "description": "sim: nanosim+esim\nsize: 129gb",
    "price": 970,
    "link": null,
    "photo": "/s3/images/wish-photo/2025/01/06/1CJwuTQRu48KCUvSuirSp6.webp",
    "photos": [
      {
        "width": 1000,
        "height": 1000,
        "url": "https://cdn.ohmywishes.com/images/wish-photo/2025/01/06/1CJwuTQRu48KCUvSuirSp6.webp",
        "thumbnails": [
          {
            "width": 500,
            "height": 500,
            "url": "https://cdn.ohmywishes.com/images/thumbnail/2025/01/06/1CJwuTWvKPZNZsUHReXD62.webp"
          }
        ]
      }
    ],
    "icon": null,
    "color": "#9b9b9b",
    "private": false,
    "visibility": "public",
    "fulfilled": true,
    "currency": "USD",
    "creatorId": "bb677c0ecd14900917853653",
    "creator": {
      "_id": "bb677c0ecd14900917853653",
      "photo": "/s3/images/user-avatar/2026/06/14/1Cc3KHPzP6XTZPGLce1sTJ.webp",
      "avatar": {
        "width": 267,
        "height": 400,
        "url": "https://cdn.ohmywishes.com/images/user-avatar/2026/06/14/1Cc3KHPzP6XTZPGLce1sTJ.webp",
        "thumbnails": [
          {
            "width": 53,
            "height": 80,
            "url": "https://cdn.ohmywishes.com/images/thumbnail/2026/06/14/1Cc3KHTg8dmFAa5jAD2bde.webp"
          },
          {
            "width": 107,
            "height": 160,
            "url": "https://cdn.ohmywishes.com/images/thumbnail/2026/06/14/1Cc3KHStVwqNEN1CNFvTw5.webp"
          }
        ]
      },
      "fullName": "vasily ",
      "firstName": "vasily",
      "lastName": "",
      "username": "dream",
      "isPro": false,
      "accountType": "user",
      "socialProfiles": ["yandex"],
      "wishesCount": 11,
      "followersCount": 1,
      "followingsCount": 1,
      "assignedWishesBySomebody": 0,
      "followedByMe": false,
      "favorite": false,
      "isFollowingRequestSent": false
    },
    "copiedByMe": false,
    "assigned": false,
    "assignedByMe": false,
    "idea": false,
    "reservedUntil": null,
    "isExternalUriBlocked": false,
    "wish_lists": [],
    "actionButtonText": "where_to_buy",
    "assignee": null,
    "oneTimeGift": false
  }
]
```

Notes:

- This list keeps the same denormalized wish shape.
- `fulfilled` is `true` for all items in this aggregate.
- The response is a raw JSON array, not wrapped.

## Wish List Actions

### `PUT /api/v3/own-user/wish-lists/{wishListId}/position`

Updates the ordering position of a wish list.

Request body observed:

```json
{}
```

Observed response:

- HTTP `200 OK`
- body: `null`

DTO candidates:

- `VoidResponseDto` or no body handling

Notes:

- This appears to be a pure ordering operation.

### `PUT /api/v3/own-user/wish-lists/{wishListId}`

Updates a wish list.

Request body observed:

```json
{
  "title": "desks",
  "description": "",
  "visibility": "inherit"
}
```

Observed response:

```json
{
  "item": {
    "id": "a46a2eb6f51a24c184415464",
    "title": "desks",
    "icon": {
      "url": "https://cdn.ohmywishes.com/images/wish-photo/2026/06/14/1Cc3KB8M7de4MEa4tcuvmM.webp",
      "width": 1000,
      "height": 1000,
      "thumbnails": [
        {
          "url": "https://cdn.ohmywishes.com/images/thumbnail/2026/06/14/1Cc3KBEmagh8Vxv6i7m6ep.webp",
          "width": 500,
          "height": 500
        }
      ]
    },
    "fullTitle": null,
    "description": null,
    "slug": "desks",
    "wishesCount": 2,
    "visibility": "by_link",
    "sharedLink": "https://ohmywishes.com/users/dream/lists/01db2f91-d875-471b-85c3-51068cdfce67"
  }
}
```

DTO candidates:

- `UpdateWishListRequestDto`
- `WishListDto`

Notes:

- `visibility` supports at least:
  - `inherit`
  - `public`
  - `followers`
  - `by_link`
- `sharedLink` appears when visibility is link-based.
- The response is wrapped in `{ item: ... }`.

## Reference Data

### `GET /api/v2/currencies`

Returns supported currencies.

Observed response:

```json
{
  "items": [
    {
      "code": "AZN",
      "symbol": "₼",
      "template": "{value}{symbol}",
      "title": "Азербайджанский манат"
    }
  ]
}
```

DTO candidates:

- `CurrencyDto`
- `CurrenciesResponseDto`

Notes:

- `symbol`, `template`, and localized `title` are all part of the currency descriptor.

### `GET /api/v3/catalogs/content-regions`

Returns available content regions.

Observed response:

```json
{
  "items": [
    {
      "code": "world",
      "title": "Мир"
    }
  ]
}
```

### `GET /api/v3/catalogs/content-regions/{regionCode}`

Returns content region metadata for a selected region.

Observed response:

```json
{
  "item": {
    "code": "russia",
    "title": "Россия",
    "currency": "RUB",
    "mainPageText": "Ohmywishes — это бесплатный онлайн-сервис ...",
    "seoDefaults": {
      "h1Text": "Ohmywishes — бесплатный онлайн-сервис ...",
      "metaKeywords": "...",
      "ogImage": {
        "url": "https://cdn.ohmywishes.com/images/region-image/2026/02/22/1CYP2XrHGuRonrwXrEVB75.png",
        "width": 1200,
        "height": 630,
        "thumbnails": []
      }
    },
    "seoTemplates": {
      "title": {
        "startPage": "...",
        "selection": "...",
        "subSelection": "..."
      },
      "description": {
        "startPage": "...",
        "secretSantaPage": "..."
      }
    },
    "links": {
      "forBusiness": "https://ohmywishes.com/business",
      "iosApp": "https://redirect.appmetrica.yandex.com/...",
      "androidApp": "https://redirect.appmetrica.yandex.com/..."
    },
    "socialNetworks": {
      "vk": null,
      "instagram": null,
      "x": null,
      "dzen": {
        "link": "https://dzen.ru/omw"
      }
    },
    "options": {
      "oauth2": {
        "vk": true,
        "apple": true,
        "telegram": true,
        "yandex": true
      },
      "auth": [
        {
          "code": "phone_number"
        }
      ],
      "captchaProviders": ["turnstile"],
      "simplifiedIdeasPage": false
    },
    "siteLogoImage": {
      "url": "https://cdn.ohmywishes.com/images/region-image/2026/04/02/1CZeiqNT98swb8VaSHPrrC.svg",
      "width": null,
      "height": null,
      "thumbnails": []
    }
  }
}
```

DTO candidates:

- `ContentRegionDto`
- `ContentRegionsResponseDto`
- `ContentRegionSeoDto`
- `ContentRegionLinksDto`
- `ContentRegionOptionsDto`

Notes:

- Region metadata is much richer than the simple list endpoint.
- This is a good candidate for localization/config bootstrap in the SDK.

## Wish Actions

### `POST /api/v3/wishes/{wishId}/reservation`

Reserves a wish for the authenticated user.

Observed response:

```json
{
  "item": {
    "id": "b86a16f2a71fc96309212854",
    "trackingId": null,
    "title": "шелковая наволочка ",
    "description": "Сон на наволочке AYRIS ...",
    "price": {
      "price": 9000,
      "currency": "RUB"
    },
    "externalUri": "https://ayris-silk.ru/catalog/pillowcases/100-silk/shyolkovaya-navolochka-50-70?mod=skystar&utm_source=ohmywishes",
    "photos": [
      {
        "url": "https://cdn.ohmywishes.com/images/wish-photo/2026/05/27/1CbTABibDiR3aH2VvEvbff.webp",
        "width": 1000,
        "height": 1000,
        "thumbnails": [
          {
            "url": "https://cdn.ohmywishes.com/images/thumbnail/2026/05/27/1CbTABso7Ss3ym6R3oAS8C.webp",
            "width": 500,
            "height": 500
          }
        ]
      }
    ],
    "icon": null,
    "isOneTimeGift": false,
    "isPrivate": false,
    "visibility": "public",
    "isFulfilled": false,
    "creator": {
      "id": "ba63dbb5c7c0cc6124659619",
      "username": "milkh8er",
      "avatar": {
        "url": "https://cdn.ohmywishes.com/images/user-avatar/2025/12/10/1CVxu38uFMhHPk1yeaEitx20254.webp",
        "width": 300,
        "height": 400,
        "thumbnails": [
          {
            "url": "https://cdn.ohmywishes.com/images/thumbnail/2025/12/10/1CVxu3KHqX1J3piecVkVe5.webp",
            "width": 60,
            "height": 80
          },
          {
            "url": "https://cdn.ohmywishes.com/images/thumbnail/2025/12/10/1CVxu3JJZ5ygnAiDK3VCsV.webp",
            "width": 120,
            "height": 160
          }
        ]
      },
      "fullname": "tasha",
      "isPro": false,
      "accountType": "user"
    },
    "isCopiedByMe": false,
    "isAssignedBySomeone": true,
    "isAssignedByMe": true,
    "isReservedBySomeone": true,
    "isReservedByMe": true,
    "reservedUntil": "2026-09-12 15:43:44",
    "isIdea": false,
    "isExternalUriBlocked": false,
    "promoCode": null,
    "wishLists": [
      {
        "id": "9c64dc875d74e45860892870",
        "title": "household"
      }
    ],
    "isExplicit": false,
    "isForeignAgentIdea": false,
    "actionButtonText": "where_to_buy",
    "canonicalUrl": null,
    "sticker": null
  }
}
```

DTO candidates:

- `ReservationResultDto`
- or reuse `WishDto`

Notes:

- This endpoint creates a reservation for a wish.
- The response is the v3 item detail shape.
- `reservedUntil` can be a timestamp string in `Y-m-d H:i:s` format.

### `DELETE /api/v3/wishes/{wishId}/reservation`

Cancels a wish reservation.

Observed response:

- HTTP `200 OK`
- body: same `item` shape as reservation response

DTO candidates:

- `ReservationCancelResultDto`
- or reuse `WishDto`

## Wishes

### `GET /api/v3/wishes/{wishId}`

Returns a single wish.

Observed response:

```json
{
  "item": {
    "id": "5d6a2ebb42660cf174445100",
    "trackingId": null,
    "title": "Sophienwald Phoenix Bordeaux",
    "description": "",
    "price": {
      "price": 8500,
      "currency": "RUB"
    },
    "externalUri": "https://sophienwald.ru/catalog/sophienwald-phoenix-bordeaux?utm_source=ohmywishes",
    "photos": [
      {
        "url": "https://cdn.ohmywishes.com/images/wish-photo/2026/06/14/1Cc3KB8M7de4MEa4tcuvmM.webp",
        "width": 1000,
        "height": 1000,
        "thumbnails": [
          {
            "url": "https://cdn.ohmywishes.com/images/thumbnail/2026/06/14/1Cc3KBEmagh8Vxv6i7m6ep.webp",
            "width": 500,
            "height": 500
          }
        ]
      }
    ],
    "icon": null,
    "isOneTimeGift": false,
    "isPrivate": false,
    "visibility": "public",
    "isFulfilled": false,
    "creator": {
      "id": "bb677c0ecd14900917853653",
      "username": "dream",
      "avatar": {
        "url": "https://cdn.ohmywishes.com/images/user-avatar/2026/06/14/1Cc3KHPzP6XTZPGLce1sTJ.webp",
        "width": 267,
        "height": 400,
        "thumbnails": [
          {
            "url": "https://cdn.ohmywishes.com/images/thumbnail/2026/06/14/1Cc3KHTg8dmFAa5jAD2bde.webp",
            "width": 53,
            "height": 80
          },
          {
            "url": "https://cdn.ohmywishes.com/images/thumbnail/2026/06/14/1Cc3KHStVwqNEN1CNFvTw5.webp",
            "width": 107,
            "height": 160
          }
        ]
      },
      "fullname": "vasily",
      "isPro": false,
      "accountType": "user"
    },
    "isCopiedByMe": false,
    "isAssignedBySomeone": false,
    "isAssignedByMe": false,
    "isReservedBySomeone": false,
    "isReservedByMe": false,
    "reservedUntil": null,
    "isIdea": false,
    "isExternalUriBlocked": false,
    "promoCode": null,
    "wishLists": [
      {
        "id": "a46a2eb6f51a24c184415464",
        "title": "desk"
      }
    ],
    "isExplicit": false,
    "isForeignAgentIdea": false,
    "actionButtonText": "where_to_buy",
    "canonicalUrl": null,
    "sticker": null
  }
}
```

DTO candidates:

- `WishDto`
- `WishPriceDto`
- `WishPhotoDto`
- `WishCreatorDto`
- `WishVisibility` enum
- `WishActionButtonText` enum

Notes:

- `price` is an object in v3 item detail.
- `wishLists` is an array of lightweight list references.
- `isPrivate` and `visibility` both describe privacy state.

### `POST /api/v2/wishes/{wishId}/copy`

Copies a wish into the authenticated user's account.

Observed response:

```json
{
  "_id": "c86a2ecb511aa17832721702",
  "trackingId": "bc69300afc14119251553063",
  "createdAt": "2026-06-14T15:40:01.000Z",
  "updatedAt": "2026-06-14T15:40:01.000Z",
  "title": "Подарочный сертификат — INVITRO",
  "description": "Подарите близким здоровье. ...",
  "price": 1000,
  "link": "https://www.invitro.ru/piter/l/podarochnye-sertifikaty?utm_source=ohmywishes",
  "photo": "/s3/images/wish-photo/2026/06/14/1Cc3QPtoiof8yce8VYy4cf.webp",
  "photos": [
    {
      "width": 507,
      "height": 613,
      "url": "https://cdn.ohmywishes.com/images/wish-photo/2026/06/14/1Cc3QPtoiof8yce8VYy4cf.webp",
      "thumbnails": []
    }
  ],
  "icon": null,
  "color": "#000000",
  "private": false,
  "visibility": "public",
  "fulfilled": false,
  "currency": "RUB",
  "creatorId": "bb677c0ecd14900917853653",
  "creator": {
    "_id": "bb677c0ecd14900917853653",
    "photo": "/s3/images/user-avatar/2026/06/14/1Cc3KHPzP6XTZPGLce1sTJ.webp",
    "avatar": {
      "width": 267,
      "height": 400,
      "url": "https://cdn.ohmywishes.com/images/user-avatar/2026/06/14/1Cc3KHPzP6XTZPGLce1sTJ.webp",
      "thumbnails": [
        {
          "width": 53,
          "height": 80,
          "url": "https://cdn.ohmywishes.com/images/thumbnail/2026/06/14/1Cc3KHTg8dmFAa5jAD2bde.webp"
        },
        {
          "width": 107,
          "height": 160,
          "url": "https://cdn.ohmywishes.com/images/thumbnail/2026/06/14/1Cc3KHStVwqNEN1CNFvTw5.webp"
        }
      ]
    },
    "fullName": "vasily ",
    "firstName": "vasily",
    "lastName": "",
    "username": "dream",
    "isPro": false,
    "accountType": "user",
    "socialProfiles": ["yandex"],
    "wishesCount": 11,
    "followersCount": 1,
    "followingsCount": 1,
    "assignedWishesBySomebody": 0,
    "followedByMe": false,
    "favorite": false,
    "isFollowingRequestSent": false
  },
  "copiedByMe": false,
  "assigned": false,
  "assignedByMe": false,
  "idea": false,
  "reservedUntil": null,
  "isExternalUriBlocked": false,
  "wish_lists": [],
  "actionButtonText": "where_to_buy",
  "assignee": null,
  "oneTimeGift": false
}
```

DTO candidates:

- `CopyWishResultDto`
- or reuse `WishListWishDto`

Notes:

- This endpoint creates a new wish from an idea/product wish.
- `trackingId` mirrors the source wish id.
- The response is the denormalized list-style wish shape.

### `GET /api/v2/users/self/wish-lists/all/wishes?size=20&page=1`

Returns the authenticated user's wishes across all wish lists.

Observed response:

- raw JSON array, not wrapped

Example item shape:

```json
{
  "_id": "5d6a2ebb42660cf174445100",
  "trackingId": null,
  "createdAt": "2026-06-14T14:31:30.000Z",
  "updatedAt": "2026-06-14T14:31:30.000Z",
  "title": "Sophienwald Phoenix Bordeaux",
  "description": "",
  "price": 8500,
  "link": "https://sophienwald.ru/catalog/sophienwald-phoenix-bordeaux?utm_source=ohmywishes",
  "photo": "/s3/images/wish-photo/2026/06/14/1Cc3KB8M7de4MEa4tcuvmM.webp",
  "photos": [
    {
      "width": 1000,
      "height": 1000,
      "url": "https://cdn.ohmywishes.com/images/wish-photo/2026/06/14/1Cc3KB8M7de4MEa4tcuvmM.webp",
      "thumbnails": [
        {
          "width": 500,
          "height": 500,
          "url": "https://cdn.ohmywishes.com/images/thumbnail/2026/06/14/1Cc3KBEmagh8Vxv6i7m6ep.webp"
        }
      ]
    }
  ],
  "icon": null,
  "color": "#b24d00",
  "private": false,
  "visibility": "public",
  "fulfilled": false,
  "currency": "RUB",
  "creatorId": "bb677c0ecd14900917853653",
  "creator": {
    "_id": "bb677c0ecd14900917853653",
    "photo": "/s3/images/user-avatar/2026/06/14/1Cc3KHPzP6XTZPGLce1sTJ.webp",
    "avatar": {
      "width": 267,
      "height": 400,
      "url": "https://cdn.ohmywishes.com/images/user-avatar/2026/06/14/1Cc3KHPzP6XTZPGLce1sTJ.webp",
      "thumbnails": [
        {
          "width": 53,
          "height": 80,
          "url": "https://cdn.ohmywishes.com/images/thumbnail/2026/06/14/1Cc3KHTg8dmFAa5jAD2bde.webp"
        },
        {
          "width": 107,
          "height": 160,
          "url": "https://cdn.ohmywishes.com/images/thumbnail/2026/06/14/1Cc3KHStVwqNEN1CNFvTw5.webp"
        }
      ]
    },
    "fullName": "vasily ",
    "firstName": "vasily",
    "lastName": "",
    "username": "dream",
    "isPro": false,
    "accountType": "user",
    "socialProfiles": ["yandex"],
    "wishesCount": 11,
    "followersCount": 1,
    "followingsCount": 1,
    "assignedWishesBySomebody": 0,
    "followedByMe": false,
    "favorite": false,
    "isFollowingRequestSent": false
  },
  "copiedByMe": false,
  "assigned": false,
  "assignedByMe": false,
  "idea": false,
  "reservedUntil": null,
  "isExternalUriBlocked": false,
  "wish_lists": [
    {
      "_id": "a46a2eb6f51a24c184415464",
      "title": "desk",
      "fullTitle": null,
      "description": null,
      "slug": "desk"
    }
  ],
  "actionButtonText": "where_to_buy",
  "assignee": null,
  "oneTimeGift": false
}
```

DTO candidates:

- `WishListWishDto`
- `WishListWishCreatorDto`
- `WishListWishPhotoDto`
- `WishListWishListRefDto`

Notes:

- This is a denormalized, list-focused wish shape.
- `price` is a scalar here, while `v3 /wishes/{id}` exposes `{ price, currency }`.

### `PUT /api/v2/users/self/wishes/{wishId}`

Updates an owned wish.

Request body observed:

```json
{
  "title": "Sophienwald Phoenix Bordeaux",
  "description": null,
  "link": "https://sophienwald.ru/catalog/sophienwald-phoenix-bordeaux?utm_source=ohmywishes",
  "price": 8500,
  "currency": "RUB",
  "private": false,
  "wish_lists": [
    "a46a2eb6f51a24c184415464"
  ]
}
```

Observed response is the same denormalized wish shape as the list endpoint.

DTO candidates:

- `UpdateWishRequestDto`
- `WishUpdateResultDto` or reuse `WishListWishDto`

Notes:

- `wish_lists` contains ids only in the request.
- `description` may be `null` in the request and `""` in the response.

## Service Status

### `GET https://status.ohmywishes.com/api/status.json`

Returns the public service status page payload.

Observed response:

```json
{
  "isEnabled": false,
  "platforms": [
    {
      "platform": "all",
      "versions": [
        {
          "version": "all",
          "color": "#F3463B",
          "messageColor": "#FFFFFF",
          "icon": "https://status.ohmywishes.com/images/warning.svg",
          "message": [
            {
              "lang": "ru",
              "text": "Наблюдаются временные ограничения ..."
            },
            {
              "lang": "default",
              "text": "We are facing temporary service outage ..."
            }
          ]
        }
      ]
    }
  ]
}
```

DTO candidates:

- `ServiceStatusDto`
- `ServiceStatusPlatformDto`
- `ServiceStatusVersionDto`
- `ServiceStatusMessageDto`

Notes:

- This is a standalone status endpoint on a separate subdomain.
- `isEnabled: false` likely means an outage/maintenance banner is active.

## Suggested DTO Layer

Core primitives:

- `ApiId`
- `MoneyDto`
- `ImageDto`
- `ImageVariantDto`
- `PaginationDto`

User-related:

- `UserId`
- `Username`
- `AvatarDto`
- `UserShortDto`
- `UserProfileDto`
- `SelfUserDto`
- `UserStatsDto`
- `UserSettingsDto`
- `BirthdayDto`

Wish list-related:

- `WishListId`
- `WishListDto`
- `WishListRefDto`
- `UpdateWishListRequestDto`

Wish-related:

- `WishId`
- `WishDto`
- `WishListWishDto`
- `WishPriceDto`
- `WishCreatorDto`
- `WishPhotoDto`
- `CopyWishResultDto`
- `ReservationResultDto`
- `ReservationCancelResultDto`

Selection-related:

- `SelectionId`
- `SelectionDto`
- `SelectionMetaDto`
- `SelectionBrandDto`
- `SelectionIdeaListDto`
- `IdeaDto`
- `IdeaPromoCodeDto`
- `IdeaPhotoDto`
- `IdeaPriceDto`

Settings and reference:

- `OwnUserSettingsDto`
- `UpdateOwnUserSettingsRequestDto`
- `NotificationSettingsResponseDto`
- `NotificationSendingSettingsDto`
- `NotificationReceivingSettingsDto`
- `NotificationChannelSettingsDto`
- `CurrencyDto`
- `CurrenciesResponseDto`
- `ContentRegionDto`
- `ContentRegionsResponseDto`
- `ContentRegionSeoDto`
- `ContentRegionLinksDto`
- `ContentRegionOptionsDto`
- `ServiceStatusDto`
- `ServiceStatusPlatformDto`
- `ServiceStatusVersionDto`
- `ServiceStatusMessageDto`

Relations:

- `UserRelationFlagsDto`
- `FavoriteToggleResultDto` if a non-empty acknowledgment is later observed

## Normalization Rules

Recommended SDK normalization:

- Map `fullName`, `fullname`, and computed name sources to a single read-only property.
- Map `photo` and `avatar` into one `AvatarDto` where possible.
- Represent wish price consistently as `MoneyDto` in domain DTOs.
- Preserve raw payloads only if a field cannot yet be normalized safely.
- Treat empty string and `null` carefully for optional text fields.

## Open Questions

- Whether `PUT /api/v2/users/self` is the only profile-update endpoint.
- Whether there is a `DELETE /api/v2/users/{id}/favorite` equivalent for users or only toggle semantics on the backend.
- Exact behavior of `query` on followings/followers endpoints.
- Whether `own-user/followers/requests` can return non-empty items for the current account and what actions are supported on them.
- Whether list and detail wish schemas diverge further on unpublished or reserved wishes.
