// --- SIMULASI DATABASE ---
const currentUser = { id: 1, username: "Balogun", role: "member" };

const categories = [
  { id: 0, name: "All" },
  { id: 1, name: "Business" },
  { id: 2, name: "Self-Help" },
  { id: 3, name: "Fiction" },
  { id: 4, name: "Science" },
];

const books = [
  {
    id: 101,
    category_id: 1,
    title: "The Psychology of Money",
    author: "Morgan Housel",
    description: "Timeless lessons on wealth, greed, and happiness.",
    cover_image:
      "https://m.media-amazon.com/images/I/81Dky+tD+pL._AC_UF1000,1000_QL80_.jpg",
    pdf_file: "money.pdf",
  },
  {
    id: 102,
    category_id: 1,
    title: "Company of One",
    author: "Paul Jarvis",
    description: "Why staying small is the next big thing for business.",
    cover_image:
      "https://m.media-amazon.com/images/I/71IGaH14FpL._AC_UF1000,1000_QL80_.jpg",
    pdf_file: "company.pdf",
  },
  {
    id: 103,
    category_id: 3,
    title: "The Bees",
    author: "Laline Paull",
    description: "A thriller set in a beehive. Unique and gripping.",
    cover_image:
      "https://m.media-amazon.com/images/I/81vkI+A6eGL._AC_UF1000,1000_QL80_.jpg",
    pdf_file: "bees.pdf",
  },
  {
    id: 104,
    category_id: 2,
    title: "Atomic Habits",
    author: "James Clear",
    description: "An easy & proven way to build good habits & break bad ones.",
    cover_image:
      "https://m.media-amazon.com/images/I/81F90H7hnML._AC_UF1000,1000_QL80_.jpg",
    pdf_file: "atomic.pdf",
  },
  {
    id: 105,
    category_id: 4,
    title: "Brief Answers",
    author: "Stephen Hawking",
    description:
      "The world-famous cosmologist leaves us with his final thoughts.",
    cover_image:
      "https://m.media-amazon.com/images/I/81q1A+l-p6L._AC_UF1000,1000_QL80_.jpg",
    pdf_file: "hawking.pdf",
  },
  {
    id: 106,
    category_id: 3,
    title: "Harry Potter",
    author: "J.K. Rowling",
    description: "The boy who lived and his adventures at Hogwarts.",
    cover_image:
      "https://m.media-amazon.com/images/I/81q77Q39nEL._AC_UF1000,1000_QL80_.jpg",
    pdf_file: "hp.pdf",
  },
  {
    id: 107,
    category_id: 2,
    title: "Subtle Art",
    author: "Mark Manson",
    description: "A counterintuitive approach to living a good life.",
    cover_image:
      "https://m.media-amazon.com/images/I/71QKQ9mwV7L._AC_UF1000,1000_QL80_.jpg",
    pdf_file: "subtle.pdf",
  },
  {
    id: 108,
    category_id: 1,
    title: "Zero to One",
    author: "Peter Thiel",
    description: "Notes on startups, or how to build the future.",
    cover_image:
      "https://m.media-amazon.com/images/I/71uAI28kJuL._AC_UF1000,1000_QL80_.jpg",
    pdf_file: "zero.pdf",
  },
];

// --- LOGIC UTAMA ---

document.addEventListener("DOMContentLoaded", () => {
  document.getElementById("userDisplay").innerText = currentUser.username;

  // 1. Render Kategori (Tags)
  loadCategories();

  // 2. Render Bagian RECOMMENDED (Misal: Ambil 4 buku pertama)
  loadRecommended(books.slice(0, 4));

  // 3. Render Bagian CATEGORIES (Awalnya load semua buku)
  loadCategoryBooks(books);

  // Default Detail View
  if (books.length > 0) showBookDetail(books[0]);
});

// Fungsi Render Kategori
function loadCategories() {
  const categoryContainer = document.getElementById("categoryList");
  categoryContainer.innerHTML = "";

  categories.forEach((cat) => {
    const span = document.createElement("span");
    span.className = "tag";
    if (cat.id === 0) span.classList.add("active"); // Default active
    span.innerText = cat.name;

    span.onclick = function () {
      // Ubah UI active tag
      document
        .querySelectorAll(".tag")
        .forEach((t) => t.classList.remove("active"));
      span.classList.add("active");

      // Filter Data untuk bagian Bawah (Categories Section)
      if (cat.id === 0) {
        loadCategoryBooks(books);
      } else {
        const filtered = books.filter((b) => b.category_id === cat.id);
        loadCategoryBooks(filtered);
      }
    };
    categoryContainer.appendChild(span);
  });
}

// Fungsi Render Grid Recommended (Target ID: recommendedContainer)
function loadRecommended(data) {
  const container = document.getElementById("recommendedContainer");
  container.innerHTML = "";

  data.forEach((book) => {
    container.appendChild(createCard(book));
  });
}

// Fungsi Render Grid Category Books (Target ID: categoryBookContainer)
function loadCategoryBooks(data) {
  const container = document.getElementById("categoryBookContainer");
  container.innerHTML = "";

  if (data.length === 0) {
    container.innerHTML =
      '<p style="color:#888; grid-column: 1/-1;">No books found in this category.</p>';
    return;
  }

  data.forEach((book) => {
    container.appendChild(createCard(book));
  });
}

// Helper: Membuat HTML Card agar tidak menulis ulang kode
function createCard(book) {
  const card = document.createElement("div");
  card.className = "book-card";
  card.onclick = () => showBookDetail(book);

  card.innerHTML = `
        <div class="cover-wrapper">
            <img src="${book.cover_image}" alt="${book.title}" class="book-cover-img">
        </div>
        <div class="book-info">
            <h3>${book.title}</h3>
            <p>${book.author}</p>
        </div>
    `;
  return card;
}

// Menampilkan Detail Buku di Panel Kanan
function showBookDetail(book) {
  document.getElementById("detailImage").src = book.cover_image;
  document.getElementById("detailTitle").innerText = book.title;
  document.getElementById("detailAuthor").innerText = book.author;
  document.getElementById("detailDescription").innerText = book.description;
  document.getElementById("detailId").innerText = book.id;

  const btn = document.getElementById("readBtn");
  btn.href = book.pdf_file;
  btn.onclick = (e) => {
    if (!book.pdf_file || book.pdf_file === "#") {
      e.preventDefault();
      alert("File PDF belum tersedia.");
    }
  };
}

// Fitur Search (Hanya memfilter bagian bawah/categories agar recommended tetap statis)
function filterBooks() {
  const query = document.getElementById("searchInput").value.toLowerCase();
  const filtered = books.filter(
    (book) =>
      book.title.toLowerCase().includes(query) ||
      book.author.toLowerCase().includes(query)
  );
  loadCategoryBooks(filtered);
}

// Sidebar Mobile
function toggleSidebar() {
  document.getElementById("sidebar").classList.toggle("active");
}
