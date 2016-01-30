package com.scop.org.itfeeds;

import android.content.DialogInterface;
import android.content.Intent;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.support.design.widget.FloatingActionButton;
import android.support.v4.widget.SwipeRefreshLayout;
import android.support.v7.app.AlertDialog;
import android.support.v7.view.ContextThemeWrapper;
import android.view.KeyEvent;
import android.view.View;
import android.support.design.widget.NavigationView;
import android.support.v4.view.GravityCompat;
import android.support.v4.widget.DrawerLayout;
import android.support.v7.app.ActionBarDrawerToggle;
import android.support.v7.app.AppCompatActivity;
import android.support.v7.widget.Toolbar;
import android.view.Menu;
import android.view.MenuItem;
import android.widget.AdapterView;
import android.widget.Button;
import android.widget.ListView;
import android.widget.TextView;

import com.scop.org.itfeeds.conn.APICall;
import com.scop.org.itfeeds.conn.APICallback;
import com.scop.org.itfeeds.data.Content;
import com.scop.org.itfeeds.data.Folder;
import com.scop.org.itfeeds.data.Feed;
import com.scop.org.itfeeds.data.LabelComparator;
import com.scop.org.itfeeds.data.Post;
import com.scop.org.itfeeds.data.Tag;
import com.scop.org.itfeeds.data.Filter;

import org.json.JSONException;
import org.json.JSONObject;

import java.util.ArrayList;
import java.util.Collection;
import java.util.Collections;
import java.util.List;

public class MainActivity extends AppCompatActivity
        implements NavigationView.OnNavigationItemSelectedListener,APICallback {
    private static final int CONN_USER_INFO=1;
    private static final int CONN_LOAD_POSTS=2;
    private static final int CONN_LOAD_MORE_POSTS=3;
    private static final int CONN_LOCK=4;

    private List<String> folderIds;
    private List<String> feedIds;
    private List<String> tagIds;
    private PostListAdapter postListAdapter;

    private boolean isLoading = false;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);
        Toolbar toolbar = (Toolbar) findViewById(R.id.toolbar);
        setSupportActionBar(toolbar);

        FloatingActionButton fab = (FloatingActionButton) findViewById(R.id.fab);
        fab.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                setLoading(true);
                if (postListAdapter.getCount() == 0)
                    new APICall(MainActivity.this).execute(Content.get().getQuery(), CONN_LOAD_POSTS + "");
                else
                    new APICall(MainActivity.this).execute(Content.get().getQuery(postListAdapter.getLastPostId()), CONN_LOAD_MORE_POSTS + "");

                //Snackbar.make(view, "Loading more content...", Snackbar.LENGTH_LONG)
                //        .setAction("Action", null).show();
            }
        });

        DrawerLayout drawer = (DrawerLayout) findViewById(R.id.drawer_layout);
        ActionBarDrawerToggle toggle = new ActionBarDrawerToggle(
            this, drawer, toolbar, R.string.navigation_drawer_open, R.string.navigation_drawer_close);
        drawer.setDrawerListener(toggle);
        toggle.syncState();

        NavigationView navigationView = (NavigationView) findViewById(R.id.nav_view);
        navigationView.setNavigationItemSelectedListener(this);

        postListAdapter = new PostListAdapter(this);
        ListView rr = (ListView)findViewById(R.id.postlistview);
        rr.setAdapter(postListAdapter);
        rr.setOnItemClickListener(new AdapterView.OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> parent, View view, int position, long id) {
                Post p = postListAdapter.getItem(position);
                if (p == null) return;

                Intent intentApp = new Intent(MainActivity.this, PostActivity.class);
                intentApp.putExtra("postId", p.getId());
                startActivityForResult(intentApp, 37);
            }
        });

        SwipeRefreshLayout swipeRefreshLayout = (SwipeRefreshLayout) findViewById(R.id.swiperefresh);
        swipeRefreshLayout.setColorSchemeResources(R.color.green,
                R.color.purple,
                R.color.yellow,
                R.color.orange);

        swipeRefreshLayout.setOnRefreshListener(new SwipeRefreshLayout.OnRefreshListener() {
            @Override
            public void onRefresh() {
                setLoading(true);
                new APICall(MainActivity.this).execute("arch?token=" + Content.get().getToken() + "&lock=" + Content.get().getLock(), CONN_USER_INFO + "");
            }
        });

        setLoading(true);
        new APICall(this).execute("arch?token=" + Content.get().getToken() + "&lock=" + Content.get().getLock(), CONN_USER_INFO + "");
    }

    @Override
    public void APIResponse(JSONObject json, int id, APICall parent) throws JSONException {
        switch(id){
            case CONN_USER_INFO:
                Content.get().reloadStructure(json);
                updateDrawer();
                new APICall(this).execute(Content.get().getQuery(), CONN_LOAD_POSTS + "");
                break;
            case CONN_LOAD_POSTS:
                postListAdapter.emptyList();
                Content.get().resetPosts();
                Content.get().addPosts(json);
                for (Post p : Content.get().getOrderedPosts()){
                    postListAdapter.add(p);
                }
                postListAdapter.notifyDataSetChanged();
                setLoading(false);
                break;
            case CONN_LOAD_MORE_POSTS:
                Content.get().addPosts(json);
                postListAdapter.emptyList();
                for (Post p : Content.get().getOrderedPosts()){
                    postListAdapter.add(p);
                }
                postListAdapter.notifyDataSetChanged();
                setLoading(false);
                break;
            case CONN_LOCK:
                String lockToken = json.getString("locktoken");
                Content.get().setLock(lockToken);
                new APICall(this).execute("arch?token=" + Content.get().getToken() + "&lock=" + Content.get().getLock(),CONN_USER_INFO+"");
                break;
            default:
                break;
        }
    }

    private void updateDrawer(){
        folderIds = new ArrayList<>();
        feedIds = new ArrayList<>();
        tagIds = new ArrayList<>();
        NavigationView navView = (NavigationView) findViewById(R.id.nav_view);
        Menu menu = navView.getMenu();
        menu.removeGroup(R.id.drawer_list_group);

        Filter filter = Content.get().getFilter();
        int fMode = filter.getMode();

        menu.add(R.id.drawer_list_group, 1, Menu.CATEGORY_ALTERNATIVE, R.string.all_posts);
        menu.findItem(1)
                .setIcon(R.drawable.ic_circle_full)
                .setCheckable(true)
                .setChecked(fMode == filter.ALL);

        Collection<Folder> content = Content.get().getFolders().values();
        List<Folder> folders = new ArrayList<>();

        Folder fnull = null;
        for (Folder f : content){
            if (f.getName().equals("null"))
                fnull = f;
            else
                folders.add(f);
        }
        Collections.sort(folders, new LabelComparator());
        for (int i=0;i<folders.size();i++){
            Folder f = folders.get(i);
            folderIds.add(f.getId());
            addFolderToMenu(menu,i,f.getLabel(),fMode==filter.FOLDER && filter.getFolder().equals(f.getId()));
        }

        if (fnull != null){
            List<Feed> feeds = fnull.getFeeds();
            Collections.sort(feeds, new LabelComparator());
            for (int i=0;i<feeds.size();i++){
                Feed f = feeds.get(i);
                feedIds.add(f.getId());
                addFeedToMenu(menu,i,f.getLabel(),fMode==filter.FEED && filter.getFeed().equals(f.getId()));
            }
        }

        List<Tag> tags = new ArrayList<>(Content.get().getTags().values());
        Collections.sort(tags, new LabelComparator());

        for (int i=0;i<tags.size();i++){
            Tag t = tags.get(i);
            tagIds.add(t.getId());
            addTagToMenu(menu, i, t.getLabel(), fMode == filter.TAG && filter.getTag().equals(t.getId()));
        }

        TextView tv = ((TextView) findViewById(R.id.textUsername));
        if (tv!=null && tv.getText().equals("")) {
            tv.setText(Content.get().getUsername());

            Button logoutButton = (Button) findViewById(R.id.logoutButton);
            logoutButton.setOnClickListener(new View.OnClickListener() {
                public void onClick(View v) {
                    SharedPreferences settings = android.preference.PreferenceManager.getDefaultSharedPreferences(MainActivity.this);
                    SharedPreferences.Editor editor = settings.edit();
                    editor.putString("tokensaved", null);
                    editor.commit();
                    finish();
                }
            });
        }
    }


    private void addFolderToMenu(Menu menu,int idx,String name, boolean checked){
        int id = (idx+1)*3;
        menu.add(R.id.drawer_list_group, id, Menu.CATEGORY_ALTERNATIVE, name);
        menu.findItem(id)
                .setIcon(R.drawable.ic_menu_folder)
                .setCheckable(true).setChecked(checked);
    }

    private void addFeedToMenu(Menu menu,int idx,String name, boolean checked){
        int id = (idx+1) * 3 + 1;
        menu.add(R.id.drawer_list_group, id, Menu.CATEGORY_SECONDARY, name);
        menu.findItem(id)
                .setIcon(R.drawable.ic_menu_arrow_right)
                .setCheckable(true).setChecked(checked);
    }

    private void addTagToMenu(Menu menu,int idx,String name, boolean checked){
        int id = (idx+1)*3+2;
        menu.add(R.id.drawer_list_group, id, Menu.CATEGORY_CONTAINER, name);
        menu.findItem(id)
                .setIcon(R.drawable.ic_menu_tag)
                .setCheckable(true).setChecked(checked);
    }

    private void setLoading(boolean val){
        isLoading = val;
        SwipeRefreshLayout swipeRefreshLayout = (SwipeRefreshLayout) findViewById(R.id.swiperefresh);
        swipeRefreshLayout.setRefreshing(val);
        FloatingActionButton fab = (FloatingActionButton) findViewById(R.id.fab);
        if (val) fab.setVisibility(View.INVISIBLE);
        else     fab.show();
    }

    @Override
    public void onBackPressed() {
        DrawerLayout drawer = (DrawerLayout) findViewById(R.id.drawer_layout);
        if (drawer.isDrawerOpen(GravityCompat.START)) {
            drawer.closeDrawer(GravityCompat.START);
        } else {
            moveTaskToBack(true);//super.onBackPressed();
        }
    }

    @Override
    public boolean onKeyUp(int keyCode, KeyEvent event) {
        if (keyCode == KeyEvent.KEYCODE_MENU) {
            DrawerLayout drawer = (DrawerLayout) findViewById(R.id.drawer_layout);
            if (drawer.isDrawerOpen(GravityCompat.START)) {
                drawer.closeDrawer(GravityCompat.START);
            } else {
                drawer.openDrawer(GravityCompat.START);
            }
        }
        return super.onKeyUp(keyCode, event);
    }

    @Override
    public boolean onCreateOptionsMenu(Menu menu) {
        // Inflate the menu; this adds items to the action bar if it is present.
        getMenuInflater().inflate(R.menu.main, menu);
        return true;
    }

    @Override
    public void onActivityResult(int requestCode, int resultCode, Intent data) {
        super.onActivityResult(requestCode, resultCode, data);

        if (requestCode == 37) {
            if (resultCode==21) {
                int idx = postListAdapter.getCount();//Content.get().getOrderedPosts().size();
                List<Post> posts = Content.get().getOrderedPosts();
                for (int j = idx; j < posts.size(); j++) {
                    postListAdapter.add(posts.get(j));
                }
            }
            postListAdapter.notifyDataSetChanged();
            updateDrawer();
        }
    }

    @Override
    public boolean onOptionsItemSelected(MenuItem item) {
        // Handle action bar item clicks here. The action bar will
        // automatically handle clicks on the Home/Up button, so long
        // as you specify a parent activity in AndroidManifest.xml.
        if (isLoading) return false;
        int id = item.getItemId();

        //noinspection SimplifiableIfStatement
        switch (id){
            case R.id.action_unread:
                boolean u = Content.get().toggleUnread();
                if (u)	item.setIcon(R.drawable.ic_circle_full_white);
                else	item.setIcon(R.drawable.ic_circle_empty_white);
                new APICall(MainActivity.this).execute(Content.get().getQuery(),CONN_LOAD_POSTS+"");
                setLoading(true);
                break;
            case R.id.action_fav:
                boolean f = Content.get().toggleFavorites();
                if (f)	item.setIcon(R.drawable.ic_heart_on);
                else	item.setIcon(R.drawable.ic_heart_off);
                new APICall(MainActivity.this).execute(Content.get().getQuery(),CONN_LOAD_POSTS+"");
                setLoading(true);
                break;
            case R.id.action_order:
                boolean o = Content.get().toggleOrder();
                if (o)	item.setIcon(R.drawable.ic_arrow_down);
                else	item.setIcon(R.drawable.ic_arrow_up);
                new APICall(MainActivity.this).execute(Content.get().getQuery(),CONN_LOAD_POSTS+"");
                setLoading(true);
                break;
        }

        return super.onOptionsItemSelected(item);
    }

    @SuppressWarnings("StatementWithEmptyBody")
    @Override
    public boolean onNavigationItemSelected(MenuItem item) {
        // Handle navigation view item clicks here.
        int id = item.getItemId();

        switch(id){
            case R.id.nav_exit:
                moveTaskToBack(true);
                break;
            case R.id.nav_unlock:
                LockDialog cdd=new LockDialog(this);
                cdd.show();
                break;
            case R.id.nav_manage:
                Intent intentApp = new Intent(MainActivity.this, SettingsActivity.class);
                //intentApp.putExtra(,);
                startActivityForResult(intentApp, 49);
                break;
            default:
                int type = id%3;
                if (id==1){
                    Content.get().viewAll();
                    new APICall(this).execute(Content.get().getQuery(),CONN_LOAD_POSTS+"");
                    break;
                }
                switch (type){
                    case 0:
                        final Folder selectedFolder = Content.get().getFolder(folderIds.get(id/3-1));
                        List<CharSequence> a = new ArrayList<CharSequence>();
                        a.add("All posts");
                        for (Feed feed:selectedFolder.getFeeds()){
                            a.add(feed.getLabel());
                        }

                        CharSequence[] titles = a.toArray(new CharSequence[a.size()]);
                        AlertDialog.Builder builder = new AlertDialog.Builder(new ContextThemeWrapper(MainActivity.this, android.R.style.Theme_Holo_Dialog));
                        builder.setTitle(R.string.feed_prompt);
                        builder.setItems(titles, new DialogInterface.OnClickListener() {
                            @Override
                            public void onClick(DialogInterface dialog, int which) {
                                if (which==0){
                                    Content.get().viewFolder(selectedFolder.getId());
                                } else {
                                    Feed f = selectedFolder.getFeeds().get(which-1);
                                    Content.get().viewFeed(f.getId());
                                }
                                new APICall(MainActivity.this).execute(Content.get().getQuery(),CONN_LOAD_POSTS+"");
                                setLoading(true);
                            }
                        });
                        builder.show();
                        //Folder f = Content.get().getFolder(folderIds.get(id/3-1));
                        //Content.get().viewFolder(f.getId());
                        //new APICall(this).execute(Content.get().getQuery(), CONN_LOAD_POSTS+"");
                        break;
                    case 1:
                        Feed fe = Content.get().getFeed(feedIds.get(id/3-1));
                        Content.get().viewFeed(fe.getId());
                        new APICall(this).execute(Content.get().getQuery(), CONN_LOAD_POSTS+"");
                        break;
                    case 2:
                        Tag t = Content.get().getTag(tagIds.get(id/3-1));
                        Content.get().viewTag(t.getId());
                        new APICall(this).execute(Content.get().getQuery(), CONN_LOAD_POSTS+"");
                        break;
                }
                setLoading(true);
                break;
        }

        DrawerLayout drawer = (DrawerLayout) findViewById(R.id.drawer_layout);
        drawer.closeDrawer(GravityCompat.START);
        return true;
    }

    public void setLock(String lock){
        setLoading(true);
        new APICall(this).execute("unlock?token=" + Content.get().getToken() + "&pass=" + lock, CONN_LOCK + "");
    }

    public void removeLock(){
        setLoading(true);
        Content.get().setLock("");
        new APICall(this).execute("arch?token=" + Content.get().getToken() + "&lock=" + Content.get().getLock(), CONN_USER_INFO+"");
    }
}
