"""Create sentences_pairing table

Revision ID: c2116e8b1149
Revises: f5ba8fc1c5da
Create Date: 2019-09-18 12:47:08.215289

"""
from alembic import op
import sqlalchemy as sa


# revision identifiers, used by Alembic.
revision = 'c2116e8b1149'
down_revision = 'f5ba8fc1c5da'
branch_labels = None
depends_on = None


def upgrade():
    op.add_column('sentences', sa.Column('is_source', sa.Boolean(), nullable=True), schema='keopsdb')

    # ### commands auto generated by Alembic - please adjust! ###
    op.create_table('sentences_pairing',
    sa.Column('id_1', sa.Integer(), nullable=False),
    sa.Column('id_2', sa.Integer(), nullable=False),
    sa.ForeignKeyConstraint(['id_1'], [u'keopsdb.sentences.id'], name=op.f('sentences_pairing_id_1_fkey')),
    sa.ForeignKeyConstraint(['id_2'], [u'keopsdb.sentences.id'], name=op.f('sentences_pairing_id_2_fkey')),
    sa.PrimaryKeyConstraint('id_1', 'id_2', name=op.f('sentences_pairing_pkey')),
    schema='keopsdb'
    )
    # ### end Alembic commands ###

    op.execute("""
        insert into keopsdb.sentences (corpus_id, source_text, target_text)
        (select corpus_id, target_text, '' from keopsdb.sentences)
    """)

    op.execute("""
        insert into keopsdb.sentences_pairing(id_1, id_2)
        (select s1.id, s2.id from keopsdb.sentences as s1, keopsdb.sentences as s2
        where s1.corpus_id = s2.corpus_id and s1.target_text = s2.source_text)
    """)

    op.execute("""
        update keopsdb.sentences set is_source = true where id in (select id_1 from keopsdb.sentences_pairing);
    """)

def downgrade():
    op.execute("""
        update keopsdb.sentences set target_text = s.source_text
        from keopsdb.sentences_pairing as sp, keopsdb.sentences as s
        where sp.id_1 = keopsdb.sentences.id and s.id = sp.id_2;
    """)
    # ### commands auto generated by Alembic - please adjust! ###
    op.drop_table('sentences_pairing', schema='keopsdb')
    # ### end Alembic commands ###

    op.execute("""
        delete from keopsdb.sentences where target_text = '' or target_text is null
    """)

    op.drop_column('sentences', 'is_source', schema='keopsdb')