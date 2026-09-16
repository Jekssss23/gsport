<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
  <div class="bg-black/60 border border-yellow-500/20 rounded-xl p-4 shadow-lg shadow-black/40">
    <div class="text-xs text-gray-400 mb-1">Total Rating (All Time)</div>
    <div class="text-2xl font-semibold text-yellow-400"><?php echo isset($stats['total_ratings']) ? number_format($stats['total_ratings']) : '0'; ?></div>
  </div>
  <div class="bg-black/60 border border-yellow-500/20 rounded-xl p-4 shadow-lg shadow-black/40">
    <div class="text-xs text-gray-400 mb-1">Rata-rata Rating</div>
    <div class="text-2xl font-semibold text-yellow-400"><?php echo isset($stats['avg_rating']) ? $stats['avg_rating'] : '0'; ?> / 5.0</div>
  </div>
  <div class="bg-black/60 border border-emerald-500/20 rounded-xl p-4 shadow-lg shadow-black/40">
    <div class="text-xs text-gray-400 mb-1">Positive Feedback</div>
    <div class="text-2xl font-semibold text-emerald-400"><?php echo isset($stats['positive_percentage']) ? $stats['positive_percentage'] : '0'; ?>%</div>
  </div>
  <div class="bg-black/60 border border-gscRed/20 rounded-xl p-4 shadow-lg shadow-black/40">
    <div class="text-xs text-gray-400 mb-1">Critical Feedback</div>
    <div class="text-2xl font-semibold text-gscRed"><?php echo isset($stats['critical_percentage']) ? $stats['critical_percentage'] : '0'; ?>%</div>
  </div>
</div>

<div class="space-y-4">
  <h3 class="text-sm font-semibold text-white uppercase tracking-wider mb-4">Feedback Pelanggan Terbaru</h3>
  
  <?php if (isset($reviews) && count($reviews) > 0): ?>
    <?php foreach ($reviews as $review): ?>
      <div class="bg-black/60 border border-gray-800 rounded-xl p-4 shadow-lg shadow-black/40 hover:border-yellow-500/30 transition <?php echo (isset($review['rating']) && $review['rating'] <= 2) ? 'border-l-4 border-l-gscRed' : ''; ?>">
        <div class="flex items-start justify-between">
          <div class="flex items-center space-x-3">
            <div class="w-10 h-10 rounded-full bg-blue-500/20 flex items-center justify-center text-blue-400 font-bold">
              <?php echo strtoupper(substr(isset($review['userName']) ? $review['userName'] : 'AN', 0, 2)); ?>
            </div>
            <div>
              <div class="text-sm font-semibold text-white"><?php echo isset($review['userName']) ? htmlspecialchars($review['userName']) : 'Anonymous'; ?></div>
              <div class="text-[10px] text-gray-500 italic">
                via Mobile App • <?php echo isset($review['createdAt']) ? date('d M Y', strtotime($review['createdAt'])) : 'Unknown'; ?>
              </div>
            </div>
          </div>
          <div class="flex text-yellow-400">
            <?php 
            $rating = isset($review['rating']) ? (int)$review['rating'] : 0;
            for ($i = 1; $i <= 5; $i++) {
              if ($i <= $rating) {
                echo '<i class="fa fa-star"></i>';
              } else {
                echo '<i class="fa fa-star-o text-gray-700"></i>';
              }
            }
            ?>
            <span class="ml-2 text-xs text-white font-bold"><?php echo $rating; ?>.0</span>
          </div>
        </div>
        <?php if (isset($review['review'])): ?>
        <p class="mt-3 text-sm text-gray-300 leading-relaxed">
          "<?php echo htmlspecialchars($review['review']); ?>"
        </p>
        <?php endif; ?>
        
        <?php if (isset($review['rating'])): ?>
        <div class="mt-3 flex items-center space-x-2">
          <span class="text-xs text-gray-400">Experience:</span>
          <span class="text-lg">
            <?php 
            $rating_map = [
              1 => '😡',
              2 => '😑', 
              3 => '☺️',
              4 => '😁',
              5 => '😍',
              // Fallbacks for older data
              'angry' => '😡',
              'neutral' => '😑', 
              'happy' => '☺️',
              'very_happy' => '😁',
              'love' => '😍'
            ];
            echo isset($rating_map[$review['rating']]) ? $rating_map[$review['rating']] : '😑';
            ?>
          </span>
        </div>
        <?php endif; ?>
        
        <?php if (isset($review['facilityName'])): ?>
        <div class="mt-3 flex flex-wrap gap-2">
          <span class="px-2 py-0.5 bg-gray-800 text-gray-400 rounded text-[10px]">#<?php echo htmlspecialchars($review['facilityName']); ?></span>
          <?php if (isset($review['staffOnDutyName'])): ?>
            <span class="px-2 py-0.5 bg-blue-900/50 text-blue-300 rounded text-[10px] flex items-center space-x-1">
              <i class="fa fa-user-circle"></i>
              <span><?php echo htmlspecialchars($review['staffOnDutyName']); ?></span>
              <span class="text-yellow-400 ml-1">
                <?php echo isset($review['staffRating']) ? $review['staffRating'] : 0; ?> ★
              </span>
            </span>
          <?php endif; ?>
        </div>
        <?php endif; ?>
      </div>
    <?php endforeach; ?>
  <?php else: ?>
    <div class="bg-black/60 border border-gray-800 rounded-xl p-8 shadow-lg shadow-black/40 text-center">
      <div class="text-gray-400">
        <i class="fa fa-star text-4xl mb-4"></i>
        <p class="text-lg">Belum ada rating dari pengguna</p>
        <p class="text-sm mt-2">Rating akan muncul setelah pengguna memberikan feedback melalui mobile app.</p>
      </div>
    </div>
  <?php endif; ?>
</div>