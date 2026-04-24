<?php
function generateUserMateriProgress($user_id, $product_id)
{
    $generated_progress = 0;

    DB::beginTransaction();
    try {
        // Get modul ID for 'Company Profile'
        $modul  = DB::table('modul')->where('nama_modul', 'Company Profile')->first();
        $materi = DB::table('materi')->where('modul_id', $modul->id)->first();
        DB::table('user_materi_progress')->insert(['user_id' => $user_id, 'materi_id' => $materi->id, 'status' => 'booked']);
        $generated_progress++;

        // Repeat for 'Problem Solving'
        $modul  = DB::table('modul')->where('nama_modul', 'Problem Solving')->first();
        $materi = DB::table('materi')->where('modul_id', $modul->id)->first();
        DB::table('user_materi_progress')->insert(['user_id' => $user_id, 'materi_id' => $materi->id, 'status' => 'booked']);
        $generated_progress++;

        // Repeat for 'Homecare'
        $modul  = DB::table('modul')->where('nama_modul', 'Homecare')->first();
        $materi = DB::table('materi')->where('modul_id', $modul->id)->first();
        DB::table('user_materi_progress')->insert(['user_id' => $user_id, 'materi_id' => $materi->id, 'status' => 'booked']);
        $generated_progress++;

        // For 'Materi Product'
        $modul  = DB::table('modul')->where('nama_modul', 'Materi Product')->first();
        $materi = DB::table('materi')->where('modul_id', $modul->id)->where('product_id', $product_id)->first();
        DB::table('user_materi_progress')->insert(['user_id' => $user_id, 'materi_id' => $materi->id, 'status' => 'booked']);
        $generated_progress++;

        // Get product details
        $product      = DB::table('products')->where('id', $product_id)->first();
        $profesi_list = explode(',', $product->profesi);

        // Check user's profesi_id
        $user = DB::table('users')->where('id', $user_id)->first();
        if (in_array($user->profesi_id, $profesi_list)) {
            // For 'Materi Dasar'
            $modul  = DB::table('modul')->where('nama_modul', 'Materi Dasar')->first();
            $materi = DB::table('materi')->where('modul_id', $modul->id)
                ->where('category_id', $product->category_id)
                ->where(function ($query) use ($product) {
                    if ($product->sub_category_id) {
                        $query->where('sub_category_id', $product->sub_category_id);
                    }
                })->first();
            DB::table('user_materi_progress')->insert(['user_id' => $user_id, 'materi_id' => $materi->id, 'status' => 'booked']);
            $generated_progress++;
        }

        // Check if generated progress is not equal to 5
        if ($generated_progress != 5) {
            throw new Exception('Training wajib 5 paket lengkap. Generate dibatalkan.');
        }

        DB::commit();
    } catch (Exception $e) {
        DB::rollback();
        throw $e;
    }
}
