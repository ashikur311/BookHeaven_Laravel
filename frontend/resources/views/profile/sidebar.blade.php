<aside>
    <section class="user-info">
        <img src="{{ asset($user->user_profile ?? 'https://images.unsplash.com/photo-1544947950-fa07a98d237f?auto=format&fit=crop&w=800&q=80') }}"
             alt="{{ $user->username }}" class="user-avatar">
        <div>
            <div class="user-name">{{ $user->username }}</div>
            <small>Member since: {{ \Carbon\Carbon::parse($user->create_time)->format('M Y') }}</small>
        </div>
    </section>

    <section>
        <nav>
            <ul>
                <li>
                    <a href="{{ Route::has('profile') ? route('profile') : url('/profile') }}"
                       class="{{ request()->is('profile') ? 'active' : '' }}">
                        <i class="fas fa-user"></i> Profile
                    </a>
                </li>
                <li>
                    <a href="{{ Route::has('wishlist') ? route('wishlist') : url('/profile/wishlist') }}"
                       class="{{ request()->is('wishlist') ? 'active' : '' }}">
                        <i class="fas fa-heart"></i> Wish List
                    </a>
                </li>
                <li><a href="{{ url('/profile/orders') }}" class="{{ request()->is('profile/orders') ? 'active' : '' }}"><i class="fas fa-shopping-bag"></i> My Orders</a></li>

                <li>
                    <a href="{{ Route::has('user.subscription') ? route('user.subscription') : url('/profile/subscriptions') }}"
                       class="{{ request()->is('profile/subscriptions') ? 'active' : '' }}">
                        <i class="fas fa-calendar-check"></i> My Subscription
                    </a>
                </li>
                <li>
  <a href="{{ route('settings') }}" class="{{ request()->is('settings') ? 'active' : '' }}">
    <i class="fas fa-cog"></i> Setting
  </a>
</li>

                <li>
                    <form method="POST"
                          action="{{ Route::has('logout') ? route('logout') : url('/logout') }}">
                        @csrf
                        <button type="submit" class="btn-link">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </button>
                    </form>
                </li>
            </ul>
        </nav>
    </section>
</aside>
