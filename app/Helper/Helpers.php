<?php

use Illuminate\Support\Str;

function generateRandomSKU($length = 8): string
{
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $sku = '';

    for ($i = 0; $i < $length; $i++) {
        $sku .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $sku;
}

function renderCategoriesOptions($categories, $parentId = 0, $prefix = "", $currentId = null, $exceptId = 0): void
{
    foreach ($categories as $category) {
        if ($category->parent_id === $parentId && $category->status && $category->id !== $exceptId) {
            $selected = ((int)$currentId === $category->id) ? 'selected' : '';
            echo '<option value="' . $category->id . '"' . $selected . '>' . $prefix . " " . $category->category_name . '</option>';
            renderCategoriesOptions($categories, $category->id, $prefix . '-', $currentId, $exceptId);
        }
    }
}

function createSlug($name, $suffixes): string
{
    // Convert the product name to lowercase
    $name = strtolower($name);

    // Replace spaces and special characters with dashes
    $name = preg_replace('/[^a-z0-9]+/', '-', $name);

    $suffixes = strtolower($suffixes);
    // Combine the product name and SKU with a dash
    return $name . '-' . $suffixes;
}
function generateSlugCategory($category_name, $code)
{
    // Convert string to slug format
    $slug = Str::slug($category_name, '-');

    // Append the code to the slug
    if ($code === null) {
        return $slug;
    }
    return $slug . '-' . $code;
}
function handleConvertAvatarByUserName($full_name): string
{
    $words = explode(' ', $full_name);
    $abbreviation = '';

    // Get the first letter of the first word
    if (count($words) >= 1) {
        $abbreviation .= strtoupper(substr($words[0], 0, 1));
    }

    // Get the first letter of the last word
    if (count($words) > 1) {
        $abbreviation .= strtoupper(substr(end($words), 0, 1));
    }

    return $abbreviation;
}

function handleReturnRandomClassColor(): string
{
    $randomNumber = rand(1, 5);
    $bgColor = '';
    switch ($randomNumber) {
        case 1:
            $bgColor = 'bg-primary';
            break;
        case 2:
            $bgColor = 'bg-success';
            break;
        case 3:
            $bgColor = 'bg-danger';
            break;
        case 4:
            $bgColor = 'bg-info';
            break;
        case 5:
            $bgColor = 'bg-warning';
            break;
    }
    return $bgColor;
}

function generateUserSlug($email, $familyName, $givenName): string
{
    $username = strtok($email, '@');

    // Combine family_name and given_name, replace spaces with hyphens
    $name = Str::slug("{$familyName} {$givenName}");

    // Generate a random code with length 6
    $randomCode = Str::random(6);

    // Combine email, name, and random code to create the final slug
    return Str::slug("{$username} {$name} {$randomCode}");
}
