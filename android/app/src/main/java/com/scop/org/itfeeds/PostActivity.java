package com.scop.org.itfeeds;

import android.content.Intent;
import android.graphics.Color;
import android.net.Uri;
import android.support.v7.app.AppCompatActivity;
import android.support.v7.widget.Toolbar;

import android.support.v4.app.Fragment;
import android.support.v4.app.FragmentManager;
import android.support.v4.app.FragmentPagerAdapter;
import android.support.v4.view.ViewPager;
import android.os.Bundle;
import android.view.LayoutInflater;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.view.ViewGroup;

import android.webkit.WebSettings;
import android.webkit.WebView;

import com.scop.org.itfeeds.conn.APICall;
import com.scop.org.itfeeds.conn.APICallback;
import com.scop.org.itfeeds.data.Content;
import com.scop.org.itfeeds.data.Post;

import org.json.JSONException;
import org.json.JSONObject;

import java.text.DateFormat;
import java.text.SimpleDateFormat;
import java.util.HashMap;
import java.util.List;

public class PostActivity extends AppCompatActivity implements APICallback {
    private static final int CONN_MARK_FAV=1;
    private static final int CONN_MARK_UNREAD=2;
    private static final int CONN_LOAD_MORE=3;

    private SectionsPagerAdapter mSectionsPagerAdapter;

    private ViewPager mViewPager;
    private Post selectedPost;
    private Menu menu;
    protected static HashMap<String,View> postViews;
    private boolean loadingMorePosts = false;
    private boolean addedPosts = false;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_post);

        String postid = this.getIntent().getStringExtra("postId");
        selectedPost = Content.get().getPost(postid);
        postViews = new HashMap<>();

        Toolbar toolbar = (Toolbar) findViewById(R.id.toolbar);
        setSupportActionBar(toolbar);
        // Create the adapter that will return a fragment for each of the three
        // primary sections of the activity.
        mSectionsPagerAdapter = new SectionsPagerAdapter(getSupportFragmentManager());

        // Set up the ViewPager with the sections adapter.
        mViewPager = (ViewPager) findViewById(R.id.container);
        mViewPager.setAdapter(mSectionsPagerAdapter);

        int preselected = Content.get().getOrderedPosts().indexOf(selectedPost);
        mViewPager.setCurrentItem(preselected);
        //mViewPager.setOffscreenPageLimit(0);

        mViewPager.addOnPageChangeListener(new ViewPager.OnPageChangeListener() {
            @Override
            public void onPageScrolled(int position, float positionOffset, int positionOffsetPixels) {
            }

            @Override
            public void onPageSelected(int position) {
                PostActivity.this.onPageSelected(position);
            }

            @Override
            public void onPageScrollStateChanged(int state) {
            }
        });
    }

    public void onPageSelected(int position){
        List<Post> posts = mSectionsPagerAdapter.getPosts();
        selectedPost = posts.get(position);
        updateIcons();
        if (selectedPost.getUnread() && menu!=null && menu.findItem(R.id.action_markread).isChecked()){
            toggleUnread();
        }

        getSupportActionBar().setTitle(selectedPost.getTitle());
        DateFormat df = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss");
        getSupportActionBar().setSubtitle(df.format(selectedPost.getDate()));

        if (position == posts.size()-1){
            if (!loadingMorePosts){
                new APICall(this).execute(Content.get().getQuery(selectedPost.getId()), CONN_LOAD_MORE + "");
                loadingMorePosts=true;
            }
        }
    }


    @Override
    public boolean onCreateOptionsMenu(Menu menu) {
        // Inflate the menu; this adds items to the action bar if it is present.
        this.menu = menu;
        getMenuInflater().inflate(R.menu.menu_post, menu);
        menu.findItem(R.id.action_markread).setChecked(
            android.preference.PreferenceManager.getDefaultSharedPreferences(this).getBoolean("automark_read",false)
        );
        return true;
    }

    @Override
    public boolean onOptionsItemSelected(MenuItem item) {
        // Handle action bar item clicks here. The action bar will
        // automatically handle clicks on the Home/Up button, so long
        // as you specify a parent activity in AndroidManifest.xml.
        int id = item.getItemId();

        switch(id){
            case R.id.action_unread:
                toggleUnread();
                break;
            case R.id.action_fav:
                toggleFavorite();
                break;
            case R.id.action_markread:
                item.setChecked(!item.isChecked());
                break;
            case R.id.action_openlink:
                Intent browserIntent = new Intent(Intent.ACTION_VIEW, Uri.parse(selectedPost.getLink()));
                startActivity(browserIntent);
                break;
            case R.id.action_refresh:
                WebView wv = (WebView) postViews.get(selectedPost.getId()).findViewById(R.id.html_content);
                wv.clearCache(true);
                wv.reload();
                break;
            default:
                finish();
                return true;
        }
        return super.onOptionsItemSelected(item);
    }

    @Override
    public void APIResponse(JSONObject json, int id, APICall parent) throws JSONException {
        Post p;
        switch(id){
            case CONN_MARK_UNREAD:
                p = (Post) parent.getContent();
                postViews.get(p.getId()).findViewById(R.id.loading_icon).setVisibility(View.INVISIBLE);
                int i = json.getInt("unread");
                if (i==1){
                    p.setUnread(true);
                    p.getFeed().addCount(1);
                    p.getFeed().getFolder().addCount(1);
                } else {
                    p.setUnread(false);
                    p.getFeed().addCount(-1);
                    p.getFeed().getFolder().addCount(-1);
                }
                if (p==selectedPost) updateIcons();
                break;

            case CONN_MARK_FAV:
                p = (Post) parent.getContent();
                postViews.get(p.getId()).findViewById(R.id.loading_icon).setVisibility(View.INVISIBLE);
                int j = json.getInt("favorite");
                p.setFavorite(j == 1);
                if (p==selectedPost) updateIcons();
                break;

            case CONN_LOAD_MORE:
                loadingMorePosts = false;
                Content.get().addPosts(json);
                addedPosts=true;
                mSectionsPagerAdapter.notifyMorePosts();
                break;
        }
    }

    public void toggleUnread(){
        postViews.get(selectedPost.getId()).findViewById(R.id.loading_icon).setVisibility(View.VISIBLE);
        String u="token="+Content.get().getToken()+"&lock="+Content.get().getLock()+"&postid="+selectedPost.getId()+"&unread=";

        u+= selectedPost.getUnread()?"0":"1";
        new APICall(this,selectedPost).execute("update_post?" + u, CONN_MARK_UNREAD + "");
    }

    public void toggleFavorite(){
        postViews.get(selectedPost.getId()).findViewById(R.id.loading_icon).setVisibility(View.VISIBLE);
        String f="token="+Content.get().getToken()+"&lock="+Content.get().getLock()+"&postid="+selectedPost.getId()+"&fav=";

        f+= selectedPost.getFavorite()?"0":"1";
        new APICall(this,selectedPost).execute("update_post?" + f, CONN_MARK_FAV+"");
    }

    public void updateIcons(){
        if (menu==null) return;
        MenuItem unread = menu.findItem(R.id.action_unread);
        MenuItem fav = menu.findItem(R.id.action_fav);
        if (selectedPost.getUnread())	unread.setIcon(R.drawable.ic_circle_full_white);
        else					unread.setIcon(R.drawable.ic_circle_empty_white);
        if (selectedPost.getFavorite())	fav.setIcon(R.drawable.ic_heart_on);
        else					fav.setIcon(R.drawable.ic_heart_off);
    }


    @Override
    public void finish(){
        if (addedPosts){
            Intent resultIntent = new Intent();
            setResult(21, resultIntent);
        }
        super.finish();
    }

    /**
     * A {@link FragmentPagerAdapter} that returns a fragment corresponding to
     * one of the sections/tabs/pages.
     */
    public class SectionsPagerAdapter extends FragmentPagerAdapter {
        private List<Post> posts;
        private boolean firstOpened = false;
        public SectionsPagerAdapter(FragmentManager fm) {
            super(fm);
            this.posts = Content.get().getOrderedPosts();
        }

        @Override
        public Fragment getItem(int position) {
            // getItem is called to instantiate the fragment for the given page.
            // Return a PlaceholderFragment (defined as a static inner class below).
            return PlaceholderFragment.newInstance(posts.get(position).getDescription(), posts.get(position).getId());
        }

        @Override
        public void setPrimaryItem(ViewGroup container, int position, Object object) {
            super.setPrimaryItem(container, position, object);
            if (!firstOpened){
                if (postViews.containsKey(posts.get(position).getId())) {
                    firstOpened=true;
                    onPageSelected(position);
                }
            }
        }

        public void notifyMorePosts() {
            this.posts = Content.get().getOrderedPosts();
            super.notifyDataSetChanged();
        }

        @Override
        public int getCount() {
            return posts.size();
        }

        @Override
        public CharSequence getPageTitle(int position) {
            return posts.get(position).getTitle();
        }
        public List<Post> getPosts(){
            return this.posts;
        }
    }

    /**
     * A placeholder fragment containing a simple view.
     */
    public static class PlaceholderFragment extends Fragment {
        /**
         * The fragment argument representing the section number for this
         * fragment.
         */
        private static final String ARG_POST_DESCRIPTION = "post_description";
        private static final String ARG_POST_ID = "post_id";

        public PlaceholderFragment() {
        }

        /**
         * Returns a new instance of this fragment for the given section
         * number.
         */
        public static PlaceholderFragment newInstance(String description, String id) {
            PlaceholderFragment fragment = new PlaceholderFragment();
            Bundle args = new Bundle();
            args.putString(ARG_POST_DESCRIPTION, description);
            args.putString(ARG_POST_ID, id);
            fragment.setArguments(args);
            return fragment;
        }

        @Override
        public View onCreateView(LayoutInflater inflater, ViewGroup container,
                                 Bundle savedInstanceState) {
            View rootView = inflater.inflate(R.layout.fragment_post, container, false);
            WebView wv = (WebView)rootView.findViewById(R.id.html_content);

            postViews.put(getArguments().getString(ARG_POST_ID), rootView);

            WebSettings ws = wv.getSettings();
            wv.setHorizontalScrollBarEnabled(true);
            wv.setVerticalScrollBarEnabled(true);

            ws.setUseWideViewPort(true);
            ws.setLoadWithOverviewMode(true);

            ws.setSupportZoom(true);
            ws.setBuiltInZoomControls(true);
            ws.setDisplayZoomControls(false);

            wv.setBackgroundColor(Color.rgb(34,34,34)); // #222
            final String style = "<style>*{background-color:transparent!important;color:#fff!important}body{font-size:2em;}p,b,h1,h2,h3,h4,h5,h6,div,img,video{height:auto;width:100%;background-color:#fff;}video{max-height:98vh}table{width:100wv;}table img{width:initial}</style>";//,unset
            wv.loadData(style + "<p>" + getArguments().getString(ARG_POST_DESCRIPTION) + "</p>", "text/html", "UTF-8");

            return rootView;
        }
    }
}
